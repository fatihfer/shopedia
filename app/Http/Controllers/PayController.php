<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Halaman bayar publik via token (mode demo). Tanpa login —
 * token acak 32 karakter adalah kuncinya. Hanya untuk simulasi,
 * bukan uang asli.
 */
class PayController extends Controller
{
    public function show(string $token): View
    {
        $order = $this->findOrder($token);
        $order->checkExpiry();
        $order->load(['items.product', 'paymentMethod']);

        return view('pay.show', compact('order'));
    }

    public function confirm(string $token): RedirectResponse
    {
        $order = $this->findOrder($token);
        $order->checkExpiry();

        abort_unless($order->isUnpaid(), 400, 'Pesanan ini sudah tidak bisa dibayar.');

        $order->update(['status' => 'processing', 'paid_at' => now()]);

        return redirect()->route('pay.show', $token)
            ->with('success', 'Pembayaran diterima! Pesanan sedang diproses.');
    }

    private function findOrder(string $token): Order
    {
        abort_unless(preg_match('/^[A-Za-z0-9]{32}$/', $token), 404);

        return Order::where('pay_token', $token)->firstOrFail();
    }
}
