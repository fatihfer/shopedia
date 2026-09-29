@extends('layouts.app')
@section('title', $product->name . ' - Shopedia')
@section('content')
<a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-sage-300 transition">← Kembali ke katalog</a>

<div class="mt-4 grid gap-6 lg:grid-cols-2">
    {{-- Gallery --}}
    <div class="overflow-hidden rounded-3xl border border-gray-200/70 bg-white dark:bg-night-800 dark:border-cream-100/15 shadow-sm">
        <div class="relative aspect-square bg-gradient-to-br from-gray-100 to-gray-200 dark:from-cream-100/5 dark:to-cream-100/10">
            @if ($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full flex-col items-center justify-center gap-3 text-gray-400 dark:text-gray-600">
                    <span class="text-7xl">🛍️</span>
                    <span class="text-xs font-bold uppercase tracking-[0.25em]">Shopedia</span>
                </div>
            @endif
            <span class="absolute top-4 left-4 rounded-full bg-black/60 px-3.5 py-1.5 text-xs font-semibold text-white backdrop-blur">{{ $product->category->name ?? 'Umum' }}</span>
        </div>
        <div class="flex items-center gap-4 border-t border-gray-100 px-6 py-4 text-sm dark:border-cream-100/10">
            <span class="inline-flex items-center gap-1.5 font-semibold">★ 4.9 <span class="font-normal text-gray-400">(128 ulasan)</span></span>
            <span class="text-gray-300 dark:text-gray-700">|</span>
            <span class="text-gray-500 dark:text-sage-300">🚚 Siap kirim hari ini</span>
        </div>
    </div>

    {{-- Info --}}
    <div class="rounded-3xl border border-gray-200/70 bg-white p-7 sm:p-9 dark:bg-night-800 dark:border-cream-100/15 shadow-sm h-fit">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-500 dark:text-sage-300">{{ $product->category->name ?? 'Umum' }}</p>
        <h1 class="mt-2 text-3xl sm:text-4xl font-black tracking-tight leading-tight">{{ $product->name }}</h1>
        <div class="mt-4 flex items-end gap-3">
            <p class="text-3xl font-black text-gradient">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
        </div>

        <div class="my-6 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent dark:via-white/10"></div>

        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400">Deskripsi</h2>
        <p class="mt-2 text-[15px] leading-7 text-gray-600 dark:text-cream-100/80">{{ $product->description ?? 'Belum ada deskripsi untuk produk ini.' }}</p>

        <div class="mt-6 rounded-2xl bg-gray-50 p-4 text-sm dark:bg-cream-100/5">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-sage-300">Stok tersedia</span>
                @if($product->stock > 0)
                    <span class="font-bold {{ $product->stock < 5 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $product->stock }} pcs</span>
                @else
                    <span class="font-bold text-red-500">Habis</span>
                @endif
            </div>
            <div class="mt-2.5 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-cream-100/10">
                <div class="h-full rounded-full bg-gradient-to-r from-sage-400 to-sage-600" style="width: {{ min(100, $product->stock) }}%"></div>
            </div>
        </div>

        <div class="mt-5">
            @if ($product->stock > 0)
                <form method="POST" action="{{ route('cart.store', $product) }}" class="flex gap-2.5">
                    @csrf
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                        class="w-24 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3.5 text-center font-bold outline-none focus:border-indigo-400 dark:focus:border-sage-500 dark:bg-cream-100/5 dark:border-cream-100/15" />
                    <button class="flex-1 rounded-2xl bg-gradient-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-6 py-3.5 font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50 hover:opacity-90 hover:shadow-xl active:scale-[0.99] transition">🛒 Tambah ke Keranjang</button>
                </form>
                <p class="mt-3 text-center text-xs text-gray-400">✅ Garansi 7 hari · 💳 Bisa COD · ↩️ Retur mudah</p>
            @else
                <button disabled class="w-full cursor-not-allowed rounded-2xl bg-gray-100 px-6 py-3.5 font-bold text-gray-400 dark:bg-cream-100/5">Stok Habis</button>
            @endif
        </div>
    </div>
</div>

@if($related->isNotEmpty())
<div class="mt-12 flex items-end justify-between">
    <h2 class="text-xl font-extrabold tracking-tight">Kamu mungkin juga suka 💜</h2>
    <a href="{{ route('products.index', ['category' => $product->category->slug ?? '']) }}" class="text-sm font-semibold text-indigo-600 dark:text-sage-300 hover:underline">Lihat semua →</a>
</div>
<div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
    @foreach($related as $r)
    <a href="{{ route('products.show', $r) }}" class="card-lift overflow-hidden rounded-3xl border border-gray-200/70 bg-white hover:shadow-xl dark:bg-night-800 dark:border-cream-100/15">
        <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-200 dark:from-cream-100/5 dark:to-cream-100/10 flex items-center justify-center text-4xl">🛍️</div>
        <div class="p-4">
            <p class="line-clamp-1 text-sm font-bold">{{ $r->name }}</p>
            <p class="mt-1 text-sm font-extrabold text-indigo-600 dark:text-sage-300">Rp {{ number_format($r->price, 0, ',', '.') }}</p>
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection
