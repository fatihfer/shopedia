@extends('layouts.app')
@section('title', 'Bayar Pesanan #' . $order->id . ' - Shopedia')
@section('content')
<div class="mx-auto max-w-md">
    <div class="rounded-3xl border border-line/70 bg-card p-7 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
        <p class="text-center text-xs font-bold uppercase tracking-[0.2em] text-cocoa-500/70">Shopedia · Simulasi Bayar</p>
        <h1 class="mt-1 text-center text-2xl font-black">Pesanan #{{ $order->id }}</h1>

        <div class="mt-5 space-y-2 text-sm">
            @foreach($order->items as $item)
            <div class="flex justify-between gap-3 border-b border-line/60 pb-2 dark:border-cream-100/10">
                <span>{{ $item->product->name ?? 'Produk' }} <span class="text-cocoa-500/70">×{{ $item->quantity }}</span></span>
                <span class="font-bold whitespace-nowrap">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
            </div>
            @endforeach
            @if($order->shipping_cost > 0)
            <div class="flex justify-between text-sm"><span class="text-cocoa-500/70">Ongkir</span><span class="font-bold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
            @endif
        </div>
        <div class="mt-3 flex justify-between font-black text-lg"><span>Total</span><span class="text-gradient">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>

        @if($order->isUnpaid())
            <form method="POST" action="{{ route('pay.confirm', $order->pay_token) }}" class="mt-5">
                @csrf
                <button class="w-full rounded-2xl bg-emerald-600 py-4 font-bold text-white shadow-lg hover:bg-emerald-700 active:scale-[0.99] transition">Bayar Rp {{ number_format($order->total_price, 0, ',', '.') }}</button>
            </form>
            <p class="mt-2 text-center text-xs text-cocoa-500/70">Mode demo — tidak ada uang asli yang berpindah.</p>
        @elseif($order->paid_at)
            <div class="mt-5 rounded-2xl bg-emerald-50 p-4 text-center text-sm font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                Lunas pada {{ $order->paid_at->format('d M Y H:i') }}. Halaman order di laptop akan ikut terupdate otomatis.
            </div>
        @else
            <div class="mt-5 rounded-2xl bg-red-50 p-4 text-center text-sm font-bold text-red-600 dark:bg-red-500/10 dark:text-red-300">
                Pesanan ini sudah {{ $order->status === 'expired' ? 'kedaluwarsa' : 'dibatalkan' }}.
            </div>
        @endif
    </div>
</div>
@endsection
