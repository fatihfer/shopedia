<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()
            ->with(['paymentMethod'])
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $order->checkExpiry();
        $order->load(['items.product', 'user', 'paymentMethod']);

        return view('orders.show', compact('order'));
    }

    public function uploadProof(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->checkExpiry();

        abort_unless($order->isUnpaid(), 400, 'Pesanan ini sudah tidak bisa dibayar.');

        $validated = $request->validate([
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $order->update([
            'payment_proof' => $validated['proof']->store('payment-proofs', 'public'),
            'status' => 'awaiting_verification',
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Bukti pembayaran terkirim, menunggu verifikasi admin.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->checkExpiry();

        abort_unless($order->isUnpaid(), 400, 'Pesanan ini sudah tidak bisa dibatalkan.');

        DB::transaction(function () use ($order) {
            $order->restoreStock();
            $order->update(['status' => 'cancelled']);
        });

        return redirect()->route('orders.index')->with('success', 'Pesanan dibatalkan, stok dikembalikan.');
    }
}
