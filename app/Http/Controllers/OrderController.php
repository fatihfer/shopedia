<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
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

    public function __construct(private readonly MidtransService $midtrans) {}

    public function paymentStatus(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->checkExpiry();

        return response()->json([
            'status' => $order->status,
            'paid' => (bool) $order->paid_at,
        ]);
    }

    /**
     * Sinkronisasi status langsung ke Midtrans (tidak bergantung webhook).
     * Dipakai tombol "Saya sudah bayar" di halaman pembayaran.
     */
    public function syncPayment(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($order->isGateway() && $order->isUnpaid() && $order->gateway_ref, 400);

        try {
            $remote = $this->midtrans->fetchStatus($order->gateway_ref);
            $status = $this->midtrans->applyStatus($order, $remote['transaction_status'], $remote['fraud_status']);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => 'Gagal menghubungi Midtrans, coba lagi.'], 502);
        }

        return response()->json([
            'status' => $status,
            'paid' => (bool) $order->fresh()->paid_at,
        ]);
    }

    public function uploadProof(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->checkExpiry();

        abort_unless($order->isUnpaid(), 400, 'Pesanan ini sudah tidak bisa dibayar.');
        abort_if($order->isGateway(), 400, 'Pesanan QRIS dibayar lewat QR, bukan bukti transfer.');

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
