@extends('layouts.app')
@section('title', 'Semua Produk - Shopedia')
@section('content')
<div class="mb-6">
    <p class="text-sm font-medium text-gray-500">Shopedia Store</p>
    <h1 class="text-3xl font-bold tracking-tight">Semua Produk</h1>
</div>

<form method="GET" action="{{ route('products.index') }}" class="mb-6 flex flex-wrap gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..." class="w-64 rounded-xl border px-4 py-2.5 text-sm">
    <select name="category" class="rounded-xl border px-4 py-2.5 text-sm">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
        @endforeach
    </select>
    <button class="rounded-xl bg-black px-5 py-2.5 text-sm font-medium text-white">Filter</button>
    @if(request('q') || request('category'))
        <a href="{{ route('products.index') }}" class="rounded-xl border px-5 py-2.5 text-sm">Reset</a>
    @endif
</form>

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @forelse ($products as $product)
        <a href="{{ route('products.show', $product) }}" class="group overflow-hidden rounded-2xl border bg-white transition hover:-translate-y-1 hover:shadow-lg">
            <div class="aspect-square bg-gray-100">
                @if ($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full items-center justify-center text-gray-400">No Image</div>
                @endif
            </div>
            <div class="p-5">
                <p class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-400">{{ $product->category->name ?? '-' }}</p>
                <h2 class="line-clamp-2 font-semibold">{{ $product->name }}</h2>
                <div class="mt-4 flex items-center justify-between">
                    <span class="font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <span class="text-sm {{ $product->stock > 0 ? 'text-gray-500' : 'text-red-500' }}">
                        {{ $product->stock > 0 ? "Stok {$product->stock}" : 'Habis' }}
                    </span>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full rounded-xl border bg-white p-10 text-center text-gray-500">Tidak ada produk.</div>
    @endforelse
</div>

<div class="mt-8">{{ $products->links() }}</div>
@endsection
