<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $orders = Order::with(['user', 'paymentMethod'])
            ->withCount('items')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $needsVerification = Order::where('status', 'awaiting_verification')->count();

        return view('admin.orders.index', compact('orders', 'status', 'needsVerification'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'user', 'paymentMethod']);

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $action = $request->string('action')->toString();

        if (in_array($action, ['accept', 'reject'], true)) {
            abort_unless($order->status === 'awaiting_verification', 400, 'Pesanan tidak menunggu verifikasi.');

            if ($action === 'accept') {
                $order->update([
                    'status' => 'processing',
                    'paid_at' => now(),
                    'rejection_reason' => null,
                ]);

                return back()->with('success', "Pembayaran order #{$order->id} diterima, pesanan diproses.");
            }

            $validated = $request->validate([
                'rejection_reason' => ['required', 'string', 'max:500'],
            ]);

            $order->update([
                'status' => 'awaiting_payment',
                'rejection_reason' => $validated['rejection_reason'],
                'expired_at' => now()->addHours(Order::EXPIRY_HOURS),
            ]);

            return back()->with('success', "Bukti pembayaran order #{$order->id} ditolak, customer diberi 24 jam lagi.");
        }

        $validated = $request->validate([
            'status' => ['required', 'in:processing,shipped,completed,cancelled'],
        ]);

        if ($validated['status'] === 'cancelled' && ! in_array($order->status, ['cancelled', 'expired'], true)) {
            DB::transaction(function () use ($order) {
                $order->restoreStock();
                $order->update(['status' => 'cancelled']);
            });

            return back()->with('success', 'Pesanan dibatalkan, stok dikembalikan.');
        }

        $order->update($validated);

        return back()->with('success', 'Status pesanan diperbarui ke ' . $validated['status'] . '.');
    }
}
