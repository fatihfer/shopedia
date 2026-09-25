@extends('layouts.app')
@section('title', 'Katalog Produk - Shopedia')
@section('content')

{{-- Hero --}}
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 text-white p-8 sm:p-12 shadow-xl shadow-indigo-600/20">
    <div class="absolute -top-20 -right-20 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
    <div class="absolute -bottom-24 -left-10 h-64 w-64 rounded-full bg-black/10 blur-2xl"></div>
    <div class="relative max-w-2xl">
        <p class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-semibold tracking-wide backdrop-blur">✨ Koleksi terbaru 2026</p>
        <h1 class="mt-4 text-3xl sm:text-5xl font-black tracking-tight leading-[1.05]">Belanja apa hari ini? <span class="text-yellow-200">Semua ada.</span></h1>
        <p class="mt-3 text-white/80 text-sm sm:text-base leading-6">Katalog modern dengan pencarian cepat, filter kategori, dan mode gelap yang nyaman di mata.</p>
        <form method="GET" action="{{ route('products.index') }}" class="mt-6 flex flex-col sm:flex-row gap-2.5">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari sneakers, kopi, skincare..."
                class="flex-1 rounded-2xl border-0 px-5 py-3.5 text-sm text-gray-900 placeholder:text-gray-400 outline-none focus:ring-4 focus:ring-white/30" />
            <button class="rounded-2xl bg-gray-950 px-6 py-3.5 text-sm font-bold hover:bg-gray-800 transition">Cari 🔍</button>
        </form>
    </div>
    <div class="relative mt-6 flex flex-wrap gap-2 text-xs">
        <span class="text-white/70 font-medium py-2">Populer:</span>
        @foreach($categories->take(5) as $cat)
            <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
               class="rounded-full bg-white/15 px-3.5 py-2 font-medium backdrop-blur hover:bg-white hover:text-gray-900 transition">{{ $cat->name }}</a>
        @endforeach
    </div>
</div>

{{-- Filter bar --}}
<div class="mt-8 flex flex-col lg:flex-row lg:items-center gap-3 justify-between">
    <div>
        <h2 class="text-xl font-extrabold tracking-tight">Jelajahi produk</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $products->total() }} produk ditemukan @if(request('q')) untuk “{{ request('q') }}” @endif</p>
    </div>
    <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap gap-2">
        <input type="hidden" name="q" value="{{ request('q') }}">
        <select name="category" onchange="this.form.submit()"
            class="rounded-full border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium dark:bg-white/5 dark:border-white/10 outline-none focus:border-indigo-400 cursor-pointer">
            <option value="">📂 Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>📦 {{ $cat->name }}</option>
            @endforeach
        </select>
        @if(request('q') || request('category'))
            <a href="{{ route('products.index') }}" class="rounded-full border border-gray-200 px-4 py-2.5 text-sm font-medium hover:bg-gray-100 dark:border-white/10 dark:hover:bg-white/10 transition">✕ Reset</a>
        @endif
    </form>
</div>

{{-- Grid --}}
<div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @forelse ($products as $product)
        <a href="{{ route('products.show', $product) }}"
           class="card-lift group overflow-hidden rounded-3xl border border-gray-200/70 bg-white hover:shadow-2xl hover:shadow-indigo-500/10 hover:border-indigo-200 dark:bg-gray-900 dark:border-white/10 dark:hover:border-indigo-500/30">
            <div class="relative aspect-square bg-gradient-to-br from-gray-100 to-gray-200 dark:from-white/5 dark:to-white/10 overflow-hidden">
                @if ($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}"
                         class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                @else
                    <div class="flex h-full flex-col items-center justify-center gap-2 text-gray-400 dark:text-gray-600">
                        <span class="text-5xl">🛍️</span>
                        <span class="text-xs font-medium uppercase tracking-widest">Shopedia</span>
                    </div>
                @endif
                @if($product->stock <= 0)
                    <span class="absolute top-3 left-3 rounded-full bg-red-500 px-3 py-1 text-[11px] font-bold text-white shadow">HABIS</span>
                @elseif($product->stock < 5)
                    <span class="absolute top-3 left-3 rounded-full bg-amber-500 px-3 py-1 text-[11px] font-bold text-white shadow">Sisa {{ $product->stock }}</span>
                @else
                    <span class="absolute top-3 left-3 rounded-full bg-emerald-500 px-3 py-1 text-[11px] font-bold text-white shadow">READY</span>
                @endif
                <span class="absolute top-3 right-3 rounded-full bg-black/50 px-3 py-1 text-[11px] font-medium text-white backdrop-blur opacity-0 group-hover:opacity-100 transition">Lihat →</span>
            </div>
            <div class="p-5">
                <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-indigo-500 dark:text-indigo-400">{{ $product->category->name ?? 'Umum' }}</p>
                <h2 class="mt-1.5 line-clamp-2 font-bold leading-6 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $product->name }}</h2>
                <div class="mt-3 flex items-center justify-between">
                    <span class="font-extrabold text-[15px]">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500 dark:bg-white/10 dark:text-gray-400">★ 4.9</span>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full rounded-3xl border border-dashed border-gray-300 bg-white p-14 text-center dark:bg-gray-900 dark:border-white/10">
            <p class="text-5xl">🔍</p>
            <p class="mt-3 font-bold">Tidak ada produk ditemukan</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Coba kata kunci atau kategori lain.</p>
            <a href="{{ route('products.index') }}" class="mt-4 inline-block rounded-full bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white dark:bg-white dark:text-gray-900">Reset filter</a>
        </div>
    @endforelse
</div>

<div class="mt-8">{{ $products->links() }}</div>
@endsection
