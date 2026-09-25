@extends('layouts.app')
@section('title', 'Keranjang - Shopedia')
@section('content')
<h1 class="text-2xl font-bold">Keranjang Belanja</h1>

@if($items->isEmpty())
    <div class="mt-6 rounded-2xl border bg-white p-10 text-center">
        <p class="text-gray-500">Keranjang masih kosong.</p>
        <a href="{{ route('products.index') }}" class="mt-4 inline-block rounded-xl bg-black px-6 py-3 text-sm font-medium text-white">Belanja Sekarang</a>
    </div>
@else
<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="space-y-4 lg:col-span-2">
        @foreach($items as $item)
        <div class="flex gap-4 rounded-2xl border bg-white p-4">
            <div class="h-24 w-24 shrink-0 rounded-xl bg-gray-100 flex items-center justify-center text-xs text-gray-400">No Image</div>
            <div class="flex-1">
                <a href="{{ route('products.show', $item['product']) }}" class="font-semibold hover:underline">{{ $item['product']->name }}</a>
                <p class="text-sm text-gray-500">Rp {{ number_format($item['product']->price, 0, ',', '.') }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" max="{{ $item['product']->stock }}" class="w-20 rounded-lg border px-2 py-1.5 text-sm">
                        <button class="rounded-lg border px-3 py-1.5 text-sm hover:bg-gray-100">Update</button>
                    </form>
                    <form method="POST" action="{{ route('cart.destroy', $item['product']) }}">
                        @csrf @method('DELETE')
                        <button class="text-sm text-red-500 hover:underline">Hapus</button>
                    </form>
                </div>
            </div>
            <div class="font-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
        </div>
        @endforeach
        <form method="POST" action="{{ route('cart.clear') }}">
            @csrf
            <button class="text-sm text-gray-500 hover:text-red-600">Kosongkan keranjang</button>
        </form>
    </div>
    <div class="h-fit rounded-2xl border bg-white p-6">
        <h2 class="font-bold">Ringkasan</h2>
        <div class="mt-4 flex justify-between text-sm"><span>Total</span><span class="font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
        <a href="{{ route('checkout.index') }}" class="mt-6 block rounded-xl bg-black py-3 text-center font-medium text-white hover:bg-gray-800">Checkout</a>
        <a href="{{ route('products.index') }}" class="mt-2 block text-center text-sm text-gray-500">Lanjut belanja</a>
    </div>
</div>
@endif
@endsection
