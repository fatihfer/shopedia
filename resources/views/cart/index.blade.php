@extends('layouts.app')
@section('title', 'Keranjang - Shopedia')
@section('content')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Keranjang 🛒</h1>
        <p class="text-sm text-gray-500 dark:text-sage-300">{{ $items->count() }} jenis produk</p>
    </div>
    <a href="{{ route('products.index') }}" class="rounded-full border border-gray-200 px-4 py-2 text-sm font-medium hover:bg-gray-100 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition">+ Belanja lagi</a>
</div>

@if($items->isEmpty())
    <div class="mt-6 rounded-3xl border border-dashed border-gray-300 bg-white p-14 text-center dark:bg-night-800 dark:border-cream-100/15">
        <p class="text-6xl">🛒</p>
        <p class="mt-4 text-lg font-extrabold">Keranjang masih kosong</p>
        <p class="text-sm text-gray-500 dark:text-sage-300">Yuk isi dengan barang favoritmu.</p>
        <a href="{{ route('products.index') }}" class="mt-5 inline-block rounded-2xl bg-gradient-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50">Jelajahi Katalog →</a>
    </div>
@else
<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="space-y-4 lg:col-span-2">
        @foreach($items as $item)
        <div class="flex gap-4 rounded-3xl border border-gray-200/70 bg-white p-4 sm:p-5 dark:bg-night-800 dark:border-cream-100/15 card-lift">
            <div class="grid h-24 w-24 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-cream-100 to-sage-200 text-3xl dark:from-cream-100/10 dark:to-cream-100/5">🛍️</div>
            <div class="flex-1 min-w-0">
                <a href="{{ route('products.show', $item['product']) }}" class="font-bold hover:text-indigo-600 dark:hover:text-indigo-400 line-clamp-1 transition">{{ $item['product']->name }}</a>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-sage-300">Rp {{ number_format($item['product']->price, 0, ',', '.') }} / pcs</p>
                <div class="mt-2.5 flex flex-wrap items-center gap-2">
                    <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" max="{{ $item['product']->stock }}"
                            class="w-20 rounded-xl border border-gray-200 bg-gray-50 px-2.5 py-2 text-sm text-center font-bold dark:bg-cream-100/5 dark:border-cream-100/15" />
                        <button class="rounded-xl bg-gray-900 px-3.5 py-2 text-xs font-bold text-white hover:bg-gray-700 dark:bg-cream-100 dark:text-night-950 transition">Update</button>
                    </form>
                    <form method="POST" action="{{ route('cart.destroy', $item['product']) }}">
                        @csrf @method('DELETE')
                        <button class="rounded-xl px-3 py-2 text-xs font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition">🗑 Hapus</button>
                    </form>
                </div>
            </div>
            <div class="text-right shrink-0">
                <p class="font-extrabold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ $item['quantity'] }} pcs</p>
            </div>
        </div>
        @endforeach
        <form method="POST" action="{{ route('cart.clear') }}">
            @csrf
            <button class="text-sm text-gray-400 hover:text-red-500 transition">Kosongkan keranjang ✕</button>
        </form>
    </div>
    <div class="h-fit rounded-3xl border border-gray-200/70 bg-white p-6 dark:bg-night-800 dark:border-cream-100/15 shadow-sm lg:sticky lg:top-24">
        <h2 class="font-extrabold">Ringkasan Belanja</h2>
        <div class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between text-gray-500 dark:text-sage-300"><span>Subtotal</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-gray-500 dark:text-sage-300"><span>Ongkir</span><span class="font-semibold text-emerald-500">GRATIS ✨</span></div>
            <div class="border-t border-dashed border-gray-200 dark:border-cream-100/15 pt-3 flex justify-between font-black text-base"><span>Total</span><span class="text-gradient">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
        </div>
        <a href="{{ route('checkout.index') }}" class="mt-5 block rounded-2xl bg-gradient-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 py-3.5 text-center font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50 hover:opacity-90 transition">Checkout Sekarang →</a>
        <p class="mt-3 text-center text-xs text-gray-400">🔒 Pembayaran aman & terenkripsi</p>
    </div>
</div>
@endif
@endsection
