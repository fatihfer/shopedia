<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = (string) config('midtrans.server_key');
        Config::$clientKey = (string) config('midtrans.client_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Minta QRIS dinamis ke Midtrans. Mengembalikan qr_string (payload EMVCo)
     * dan qr_image_url (URL gambar QR, untuk Payment Simulator).
     *
     * @return array{qr_string: string, qr_image_url: ?string}
     *
     * @throws \Exception
     */
    public function chargeQris(Order $order, string $gatewayOrderId): array
    {
        $response = CoreApi::charge([
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $gatewayOrderId,
                'gross_amount' => (int) round((float) $order->total_price),
            ],
            'customer_details' => [
                'first_name' => $order->recipient_name,
                'email' => $order->user->email,
                'phone' => $order->recipient_phone,
            ],
            'qris' => ['acquirer' => 'gopay'],
            'custom_expiry' => ['expiry_duration' => 30, 'unit' => 'minute'],
        ]);

        if (empty($response->qr_string)) {
            throw new \Exception('Midtrans tidak mengembalikan qr_string.');
        }

        $qrImageUrl = null;
        foreach ($response->actions ?? [] as $action) {
            $action = (array) $action;
            if (($action['name'] ?? '') === 'generate-qr-code' && ! empty($action['url'])) {
                $qrImageUrl = $action['url'];
                break;
            }
        }

        return ['qr_string' => $response->qr_string, 'qr_image_url' => $qrImageUrl];
    }

    /** Verifikasi signature_key webhook: SHA512(order_id + status_code + gross_amount + serverKey). */
    public function isValidSignature(array $data): bool
    {
        if (! isset($data['order_id'], $data['status_code'], $data['gross_amount'], $data['signature_key'])) {
            return false;
        }

        $expected = hash('sha512', $data['order_id'] . $data['status_code'] . $data['gross_amount'] . config('midtrans.server_key'));

        return hash_equals($expected, (string) $data['signature_key']);
    }

    /**
     * Tanya status terbaru langsung ke Midtrans (outbound, tidak butuh webhook).
     *
     * @return array{transaction_status: string, fraud_status: string}
     *
     * @throws \Exception
     */
    public function fetchStatus(string $gatewayRef): array
    {
        $this->boot();

        $response = (array) Transaction::status($gatewayRef);

        return [
            'transaction_status' => $response['transaction_status'] ?? 'pending',
            'fraud_status' => $response['fraud_status'] ?? '',
        ];
    }

    /**
     * Terapkan status Midtrans ke order. Dipakai webhook maupun sinkronisasi manual.
     * Mengembalikan status order setelah diterapkan.
     */
    public function applyStatus(Order $order, string $transaction, string $fraud = ''): string
    {
        if (in_array($transaction, ['capture', 'settlement'], true)) {
            if ($fraud === 'challenge') {
                return $order->status;
            }

            if ($order->isUnpaid()) {
                $order->update(['status' => 'processing', 'paid_at' => now()]);
            }

            return $order->fresh()->status;
        }

        if ($transaction === 'expire') {
            $order->checkExpiry();

            if ($order->isUnpaid()) {
                DB::transaction(function () use ($order) {
                    $order->restoreStock();
                    $order->update(['status' => 'expired']);
                });
            }

            return $order->fresh()->status;
        }

        if (in_array($transaction, ['cancel', 'deny'], true)
            && ! in_array($order->status, ['cancelled', 'expired'], true)
        ) {
            DB::transaction(function () use ($order) {
                if ($order->isUnpaid()) {
                    $order->restoreStock();
                }
                $order->update(['status' => 'cancelled']);
            });

            return $order->fresh()->status;
        }

        return $order->status;
    }

    private function boot(): void
    {
        Config::$serverKey = (string) config('midtrans.server_key');
        Config::$clientKey = (string) config('midtrans.client_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
}
