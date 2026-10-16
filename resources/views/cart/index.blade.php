@extends('layouts.app')
@section('title', 'Keranjang - Shopedia')
@section('content')
<div class="flex items-center justify-between">
    <div>
        <h1 class="flex items-center gap-2.5 text-2xl sm:text-3xl font-black tracking-tight">
            <x-icon name="cart" class="h-8 w-8 text-ember-600 dark:text-sage-300" /> Keranjang
        </h1>
        <p class="mt-1 text-sm text-cocoa-500 dark:text-sage-300">{{ $items->count() }} jenis produk</p>
    </div>
    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 rounded-full border border-line px-4 py-2 text-sm font-medium hover:bg-sand-200 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition"><x-icon name="plus" class="h-4 w-4" /> Belanja lagi</a>
</div>

@if($items->isEmpty())
    <div class="mt-6 rounded-3xl border border-dashed border-sand-300 bg-card p-14 text-center dark:bg-night-800 dark:border-cream-100/15">
        <x-icon name="cart" class="mx-auto h-16 w-16 text-sand-300 dark:text-sage-500" />
        <p class="mt-4 text-lg font-extrabold">Keranjang masih kosong</p>
        <p class="text-sm text-cocoa-500 dark:text-sage-300">Yuk isi dengan barang favoritmu.</p>
        <a href="{{ route('products.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-2xl bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50">Jelajahi Katalog <x-icon name="arrow-right" class="h-4 w-4" /></a>
    </div>
@else
<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="space-y-4 lg:col-span-2">
        @foreach($items as $item)
        <div class="flex gap-4 rounded-3xl border border-line/70 bg-card p-4 sm:p-5 dark:bg-night-800 dark:border-cream-100/15 card-lift">
            <div class="grid h-24 w-24 shrink-0 place-items-center overflow-hidden rounded-2xl bg-linear-to-br from-cream-100 to-sage-200 dark:from-cream-100/10 dark:to-cream-100/5">
                @if($item['product']->image_path)
                    <img src="{{ asset('storage/' . $item['product']->image_path) }}" alt="{{ $item['product']->name }}" class="h-full w-full object-cover">
                @else
                    <x-icon name="bag" class="h-10 w-10 text-cocoa-500/50 dark:text-sage-400/60" />
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <a href="{{ route('products.show', $item['product']) }}" class="font-bold hover:text-cocoa-700 dark:hover:text-sage-300 line-clamp-1 transition">{{ $item['product']->name }}</a>
                <p class="mt-0.5 text-sm text-cocoa-500 dark:text-sage-300">Rp {{ number_format($item['product']->price, 0, ',', '.') }} / pcs</p>
                <div class="mt-2.5 flex flex-wrap items-center gap-2">
                    {{-- Kurangi --}}
                    <form method="POST" action="{{ route('cart.update', $item['product']) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                        <button title="{{ $item['quantity'] <= 1 ? 'Hapus dari keranjang' : 'Kurangi' }}"
                            class="grid h-9 w-9 place-items-center rounded-full border border-line hover:bg-sand-200 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition">
                            <x-icon name="{{ $item['quantity'] <= 1 ? 'trash' : 'minus' }}" class="h-4 w-4 {{ $item['quantity'] <= 1 ? 'text-red-500' : '' }}" />
                        </button>
                    </form>
                    <span class="min-w-8 text-center font-extrabold tabular-nums">{{ $item['quantity'] }}</span>
                    {{-- Tambah --}}
                    <form method="POST" action="{{ route('cart.update', $item['product']) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                        <button title="Tambah" @disabled($item['quantity'] >= $item['product']->stock)
                            class="grid h-9 w-9 place-items-center rounded-full bg-night-950 text-cream-100 hover:bg-night-800 disabled:cursor-not-allowed disabled:opacity-30 dark:bg-cream-100 dark:text-night-950 dark:hover:bg-cream-200 transition">
                            <x-icon name="plus" class="h-4 w-4" />
                        </button>
                    </form>
                    <span class="ml-1 text-xs text-cocoa-500/70">Stok {{ $item['product']->stock }}</span>
                </div>
            </div>
            <div class="text-right shrink-0">
                <p class="font-extrabold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-cocoa-500/70">{{ $item['quantity'] }} pcs</p>
            </div>
        </div>
        @endforeach
        <form method="POST" action="{{ route('cart.clear') }}">
            @csrf
            <button class="inline-flex items-center gap-1.5 text-sm text-cocoa-500/70 hover:text-red-500 transition"><x-icon name="trash" class="h-4 w-4" /> Kosongkan keranjang</button>
        </form>
    </div>
    <div class="h-fit rounded-3xl border border-line/70 bg-card p-6 dark:bg-night-800 dark:border-cream-100/15 shadow-sm lg:sticky lg:top-24">
        <h2 class="font-extrabold">Ringkasan Belanja</h2>
        <div class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between text-cocoa-500 dark:text-sage-300"><span>Subtotal</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-cocoa-500 dark:text-sage-300"><span>Ongkir</span><span class="inline-flex items-center gap-1 font-semibold text-emerald-500"><x-icon name="truck" class="h-4 w-4" /> Dihitung saat checkout</span></div>
            <div class="border-t border-dashed border-line dark:border-cream-100/15 pt-3 flex justify-between font-black text-base"><span>Total</span><span class="text-gradient">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
        </div>
        <a href="{{ route('checkout.index') }}" class="mt-5 flex items-center justify-center gap-2 rounded-2xl bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 py-3.5 text-center font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50 hover:opacity-90 transition">Checkout Sekarang <x-icon name="arrow-right" class="h-4 w-4" /></a>
        <p class="mt-3 flex items-center justify-center gap-1.5 text-center text-xs text-cocoa-500/70"><x-icon name="shield" class="h-4 w-4" /> Pembayaran aman & terenkripsi</p>
    </div>
</div>
@endif
@endsection
