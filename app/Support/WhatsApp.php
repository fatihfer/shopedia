<?php

namespace App\Support;

use App\Models\Order;

class WhatsApp
{
    /** Normalisasi nomor HP Indonesia ke format internasional tanpa + (628xx). */
    public static function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (! str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }

        return strlen($digits) >= 10 ? $digits : null;
    }

    public static function waMeUrl(string $phone, string $message = ''): string
    {
        $url = 'https://wa.me/' . $phone;

        return $message === '' ? $url : $url . '?text=' . rawurlencode($message);
    }

    /** Susun teks struk pembayaran untuk dikirim via WA. */
    public static function receiptMessage(Order $order): string
    {
        if (! $order->relationLoaded('items') || ! $order->relationLoaded('paymentMethod')) {
            $order->loadMissing(['items.product', 'paymentMethod']);
        }

        $lines = [
            '✅ *Pembayaran DITERIMA — Shopedia*',
            "No. Order: #{$order->id}",
            '',
        ];

        foreach ($order->items as $item) {
            $name = $item->product->name ?? 'Produk';
            $subtotal = number_format($item->price * $item->quantity, 0, ',', '.');
            $lines[] = "• {$name} ({$item->quantity}x) — Rp{$subtotal}";
        }

        if ($order->shipping_cost > 0) {
            $lines[] = 'Ongkir: Rp' . number_format($order->shipping_cost, 0, ',', '.');
        }

        $lines[] = 'Total *LUNAS*: Rp' . number_format($order->total_price, 0, ',', '.');
        $lines[] = 'Via: ' . ($order->paymentMethod->name ?? '-');
        $lines[] = '';
        $lines[] = 'Pesanan sedang diproses, dikirim 1–2 hari kerja. Terima kasih sudah belanja! 🙏';

        return implode("\n", $lines);
    }
}
