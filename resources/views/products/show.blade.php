@extends('layouts.app')
@section('title', $product->name . ' - Shopedia')
@section('content')
<a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-cocoa-500 hover:text-cocoa-700 dark:text-sage-300 transition"><x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke katalog</a>

<div class="mt-4 grid gap-6 lg:grid-cols-2">
    {{-- Gallery --}}
    <div class="overflow-hidden rounded-3xl border border-line/70 bg-card dark:bg-night-800 dark:border-cream-100/15 shadow-sm">
        <div class="relative aspect-square bg-linear-to-br from-petal-100 to-sand-200 dark:from-cream-100/5 dark:to-cream-100/10">
            @if ($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full flex-col items-center justify-center gap-3 text-cocoa-500/70 dark:text-sage-400">
                    <x-icon name="bag" class="h-20 w-20 opacity-60" />
                    <span class="text-xs font-bold uppercase tracking-[0.25em]">Shopedia</span>
                </div>
            @endif
            <span class="absolute top-4 left-4 rounded-full bg-black/60 px-3.5 py-1.5 text-xs font-semibold text-white backdrop-blur">{{ $product->category->name ?? 'Umum' }}</span>
        </div>
        <div class="flex items-center gap-4 border-t border-line/60 px-6 py-4 text-sm dark:border-cream-100/10">
            <span class="inline-flex items-center gap-1.5 text-cocoa-500 dark:text-sage-300"><x-icon name="truck" class="h-4 w-4" /> Siap kirim hari ini</span>
        </div>
    </div>

    {{-- Info --}}
    <div class="rounded-3xl border border-line/70 bg-card p-7 sm:p-9 dark:bg-night-800 dark:border-cream-100/15 shadow-sm h-fit">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-cocoa-500 dark:text-sage-300">{{ $product->category->name ?? 'Umum' }}</p>
        <h1 class="mt-2 text-3xl sm:text-4xl font-black tracking-tight leading-tight">{{ $product->name }}</h1>
        <div class="mt-4 flex items-end gap-3">
            <p class="text-3xl font-black text-gradient">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
        </div>

        <div class="my-6 h-px bg-linear-to-r from-transparent via-gray-200 to-transparent dark:via-white/10"></div>

        <h2 class="text-sm font-bold uppercase tracking-wider text-cocoa-500/70">Deskripsi</h2>
        <p class="mt-2 text-[15px] leading-7 text-cocoa-700 dark:text-cream-100/80">{{ $product->description ?? 'Belum ada deskripsi untuk produk ini.' }}</p>

        <div class="mt-6 rounded-2xl bg-milk-100 p-4 text-sm dark:bg-cream-100/5">
            <div class="flex items-center justify-between">
                <span class="text-cocoa-500 dark:text-sage-300">Stok tersedia</span>
                @if($product->stock > 0)
                    <span class="font-bold {{ $product->stock < 5 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $product->stock }} pcs</span>
                @else
                    <span class="font-bold text-red-500">Habis</span>
                @endif
            </div>
            <div class="mt-2.5 h-2 overflow-hidden rounded-full bg-sand-200 dark:bg-cream-100/10">
                <div class="h-full rounded-full bg-linear-to-r from-sage-400 to-sage-600" style="width: {{ min(100, $product->stock) }}%"></div>
            </div>
        </div>

        <div class="mt-5">
            @if ($product->stock > 0)
                <form method="POST" action="{{ route('cart.store', $product) }}" class="flex gap-2.5">
                    @csrf
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                        class="w-24 rounded-2xl border border-line bg-milk-100 px-4 py-3.5 text-center font-bold outline-none focus:border-apricot-400 dark:focus:border-sage-500 dark:bg-cream-100/5 dark:border-cream-100/15" />
                    <button class="flex flex-1 items-center justify-center gap-2 rounded-2xl bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-6 py-3.5 font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50 hover:opacity-90 hover:shadow-xl active:scale-[0.99] transition"><x-icon name="cart" class="h-5 w-5" /> Tambah ke Keranjang</button>
                </form>
                <p class="mt-3 flex items-center justify-center gap-3 text-center text-xs text-cocoa-500/70">
                    <span class="inline-flex items-center gap-1"><x-icon name="shield" class="h-3.5 w-3.5" /> Garansi 7 hari</span> ·
                    <span class="inline-flex items-center gap-1"><x-icon name="money" class="h-3.5 w-3.5" /> Bisa COD</span> ·
                    <span class="inline-flex items-center gap-1"><x-icon name="undo" class="h-3.5 w-3.5" /> Retur mudah</span>
                </p>
            @else
                <button disabled class="w-full cursor-not-allowed rounded-2xl bg-sand-200/60 px-6 py-3.5 font-bold text-cocoa-500/70 dark:bg-cream-100/5">Stok Habis</button>
            @endif
        </div>
    </div>
</div>

@if($related->isNotEmpty())
<div class="mt-12 flex items-end justify-between">
    <h2 class="flex items-center gap-2 text-xl font-extrabold tracking-tight">Kamu mungkin juga suka <x-icon name="sparkles" class="h-5 w-5 text-ember-600 dark:text-sage-300" /></h2>
    <a href="{{ route('products.index', ['category' => $product->category->slug ?? '']) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-cocoa-700 dark:text-sage-300 hover:underline">Lihat semua <x-icon name="arrow-right" class="h-4 w-4" /></a>
</div>
<div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
    @foreach($related as $r)
    <a href="{{ route('products.show', $r) }}" class="card-lift overflow-hidden rounded-3xl border border-line/70 bg-card hover:shadow-xl dark:bg-night-800 dark:border-cream-100/15">
        <div class="flex aspect-square items-center justify-center overflow-hidden bg-linear-to-br from-petal-100 to-sand-200 dark:from-cream-100/5 dark:to-cream-100/10">
            @if($r->image_path)
                <img src="{{ asset('storage/' . $r->image_path) }}" alt="{{ $r->name }}" class="h-full w-full object-cover">
            @else
                <x-icon name="bag" class="h-12 w-12 text-cocoa-500/40 dark:text-sage-400/50" />
            @endif
        </div>
        <div class="p-4">
            <p class="line-clamp-1 text-sm font-bold">{{ $r->name }}</p>
            <p class="mt-1 text-sm font-extrabold text-cocoa-700 dark:text-sage-300">Rp {{ number_format($r->price, 0, ',', '.') }}</p>
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection
