@extends('layouts.app')
@section('title', 'Pesanan #' . $order->id)
@section('content')
<a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-cocoa-500 hover:text-cocoa-700 dark:text-sage-300 transition">← Semua pesanan</a>
<div class="mt-4 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-3xl border border-line/70 bg-card p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-black">Pesanan #{{ $order->id }}</h1>
            <span class="rounded-full bg-sand-200/60 px-3.5 py-1.5 text-xs font-bold uppercase dark:bg-cream-100/10">{{ $order->status }}</span>
        </div>
        <p class="mt-1 text-sm text-cocoa-500 dark:text-sage-300">
            {{ $order->created_at->format('d M Y H:i') }} · {{ $order->user->name }} ({{ $order->user->email }}) · via {{ $order->paymentMethod->name ?? '-' }}
            @if($order->paid_at) · lunas {{ $order->paid_at->format('d M Y H:i') }} @endif
        </p>
        <div class="mt-4 space-y-2 text-sm">
            @foreach($order->items as $item)
            <div class="flex justify-between gap-3 rounded-2xl bg-milk-100 p-3 dark:bg-cream-100/5"><span class="truncate">{{ $item->product->name ?? '-' }} × {{ $item->quantity }}</span><span class="font-bold whitespace-nowrap">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span></div>
            @endforeach
        </div>
        <p class="mt-4 flex justify-between font-black text-lg"><span>Total</span><span class="text-gradient">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></p>
        <p class="mt-4 text-sm rounded-2xl bg-milk-100 p-4 dark:bg-cream-100/5"><span class="font-bold">📍 Alamat:</span> {{ $order->shipping_address }}</p>

        @if($order->payment_proof)
        <h2 class="mt-6 font-extrabold">🧾 Bukti Pembayaran</h2>
        <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank">
            <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti pembayaran #{{ $order->id }}" class="mt-2 max-h-96 rounded-2xl border border-line dark:border-cream-100/15 hover:opacity-90 transition">
        </a>
        @if($order->rejection_reason)
        <p class="mt-2 text-sm text-red-500">Alasan tolak sebelumnya: {{ $order->rejection_reason }}</p>
        @endif
        @endif
    </div>

    <div class="space-y-6">
        @if($order->status === 'awaiting_verification')
        <div class="rounded-3xl border border-amber-300 bg-amber-50 p-6 dark:bg-amber-500/10 dark:border-amber-500/30">
            <h2 class="font-extrabold">🔍 Verifikasi Pembayaran</h2>
            <p class="mt-1 text-xs text-cocoa-500/70">Cek bukti di samping (bandingkan nominal & tujuan), lalu terima atau tolak.</p>
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-3">
                @csrf @method('PATCH')
                <input type="hidden" name="action" value="accept">
                <button class="w-full rounded-2xl bg-emerald-600 py-3 text-sm font-bold text-white hover:bg-emerald-700 transition">✅ Terima & Proses</button>
            </form>
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-2">
                @csrf @method('PATCH')
                <input type="hidden" name="action" value="reject">
                <input name="rejection_reason" required maxlength="500" placeholder="Alasan tolak, misal: nominal kurang"
                    class="w-full rounded-2xl border border-line bg-card px-4 py-2.5 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none">
                <button class="mt-2 w-full rounded-2xl border border-red-300 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition">❌ Tolak Bukti</button>
            </form>
        </div>
        @else
        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="h-fit rounded-3xl border border-line/70 bg-card p-6 dark:bg-night-800 dark:border-cream-100/15">
            @csrf @method('PATCH')
            <h2 class="font-extrabold">🔄 Update Status</h2>
            <p class="text-xs text-cocoa-500/70 mt-1">Status saat ini: <span class="font-bold uppercase">{{ $order->status }}</span></p>
            <select name="status" class="mt-3 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none focus:border-apricot-400 dark:focus:border-sage-500">
                @foreach(['processing','shipped','completed','cancelled'] as $s)
                <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="mt-3 w-full rounded-2xl bg-gray-900 py-3 text-sm font-bold text-white hover:bg-gray-700 dark:bg-cream-100 dark:text-night-950 transition">Simpan Status</button>
        </form>
        @endif
    </div>
</div>
@endsection
