@extends('layouts.app')
@section('title', 'Checkout - Shopedia')
@section('content')
<div class="mx-auto max-w-5xl">
    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Checkout 💳</h1>
    <p class="text-sm text-cocoa-500 dark:text-sage-300">Satu langkah lagi pesananmu diproses.</p>

    {{-- Steps --}}
    <div class="mt-5 flex items-center gap-2 text-xs font-semibold">
        <span class="rounded-full bg-emerald-500 px-3.5 py-1.5 text-white">1 Keranjang ✓</span>
        <span class="text-sand-300">─</span>
        <span class="rounded-full bg-indigo-600 px-3.5 py-1.5 text-white shadow shadow-night-900/25 dark:shadow-black/50">2 Checkout</span>
        <span class="text-sand-300">─</span>
        <span class="rounded-full bg-sand-200 px-3.5 py-1.5 text-cocoa-500 dark:bg-cream-100/10 dark:text-sage-300">3 Selesai</span>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ route('checkout.store') }}" class="lg:col-span-2 rounded-3xl border border-line/70 bg-card p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
            @csrf
            <h2 class="font-extrabold flex items-center gap-2">📍 Alamat Pengiriman</h2>
            <textarea name="shipping_address" required rows="5" placeholder="Contoh: Budi Santoso, Jl. Mawar No. 10, Bandung 40111, HP 0812-3456-7890"
                class="mt-3 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3.5 text-sm leading-6 outline-none focus:border-apricot-400 dark:focus:border-sage-500 focus:ring-4 focus:ring-apricot-400/40 dark:focus:ring-sage-500/20 dark:bg-cream-100/5 dark:border-cream-100/15 transition">{{ old('shipping_address') }}</textarea>
            <div class="mt-4 grid sm:grid-cols-3 gap-2.5 text-xs">
                <div class="rounded-2xl border border-line p-3 dark:border-cream-100/15"><p class="font-bold">🚚 Reguler</p><p class="text-cocoa-500/70">2-3 hari · Gratis</p></div>
                <div class="rounded-2xl border border-apricot-400 bg-sand-200/60 p-3 dark:bg-sage-500/15 dark:border-sage-500/30"><p class="font-bold text-cocoa-700 dark:text-sage-200">⚡ Express ✓</p><p class="text-cocoa-500/70">1 hari · Gratis</p></div>
                <div class="rounded-2xl border border-line p-3 dark:border-cream-100/15"><p class="font-bold">📦 Hemat</p><p class="text-cocoa-500/70">4-5 hari · Gratis</p></div>
            </div>
            <button class="mt-5 w-full rounded-2xl bg-gradient-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 py-4 font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50 hover:opacity-90 active:scale-[0.99] transition">Buat Pesanan — Rp {{ number_format($total, 0, ',', '.') }}</button>
            <p class="mt-2.5 text-center text-xs text-cocoa-500/70">Dengan memesan, kamu menyetujui syarat & ketentuan Shopedia.</p>
        </form>
        <div class="h-fit rounded-3xl border border-line/70 bg-card p-6 dark:bg-night-800 dark:border-cream-100/15 lg:sticky lg:top-24">
            <h2 class="font-extrabold">Pesananmu ({{ $items->count() }})</h2>
            <div class="mt-3 max-h-64 overflow-auto space-y-2.5 text-sm pr-1">
                @foreach($items as $item)
                <div class="flex justify-between gap-3 rounded-2xl bg-milk-100 p-3 dark:bg-cream-100/5">
                    <span class="line-clamp-1">{{ $item['product']->name }} <span class="text-cocoa-500/70">×{{ $item['quantity'] }}</span></span>
                    <span class="font-bold whitespace-nowrap">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-4 border-t border-dashed border-line dark:border-cream-100/15 pt-3 flex justify-between font-black"><span>Total</span><span class="text-gradient">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
        </div>
    </div>
</div>
@endsection
