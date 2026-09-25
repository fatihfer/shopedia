@extends('layouts.app')
@section('title', $product->name . ' - Shopedia')
@section('content')
<a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-black">&larr; Kembali</a>
<div class="mt-4 grid gap-10 md:grid-cols-2">
    <div class="overflow-hidden rounded-3xl border bg-white">
        <div class="aspect-square bg-gray-100">
            @if ($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full items-center justify-center text-gray-400">No Image</div>
            @endif
        </div>
    </div>
    <div>
        <p class="text-sm font-medium uppercase tracking-wide text-gray-400">{{ $product->category->name ?? '-' }}</p>
        <h1 class="mt-2 text-4xl font-bold tracking-tight">{{ $product->name }}</h1>
        <p class="mt-4 text-2xl font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
        <div class="my-6 h-px bg-gray-200"></div>
        <p class="leading-7 text-gray-600">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
        <div class="mt-6">
            @if ($product->stock > 0)
                <p class="text-sm text-gray-500">{{ $product->stock }} tersedia</p>
                <form method="POST" action="{{ route('cart.store', $product) }}" class="mt-3 flex gap-3">
                    @csrf
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-24 rounded-xl border px-4 py-3">
                    <button class="flex-1 rounded-xl bg-black px-6 py-3 font-medium text-white hover:bg-gray-800">Add to Cart</button>
                </form>
            @else
                <p class="font-medium text-red-500">Stok habis</p>
                <button disabled class="mt-4 w-full cursor-not-allowed rounded-xl bg-gray-200 px-6 py-3 text-gray-400">Out of Stock</button>
            @endif
        </div>
    </div>
</div>

@if($related->isNotEmpty())
<h2 class="mb-4 mt-12 text-xl font-bold">Produk Terkait</h2>
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
    @foreach($related as $r)
    <a href="{{ route('products.show', $r) }}" class="overflow-hidden rounded-2xl border bg-white hover:shadow-lg">
        <div class="aspect-square bg-gray-100 flex items-center justify-center text-gray-400 text-sm">No Image</div>
        <div class="p-4">
            <p class="text-sm font-semibold line-clamp-1">{{ $r->name }}</p>
            <p class="mt-1 text-sm font-bold">Rp {{ number_format($r->price, 0, ',', '.') }}</p>
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection
