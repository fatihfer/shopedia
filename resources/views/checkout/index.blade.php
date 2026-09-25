@extends('layouts.app')
@section('title', 'Checkout - Shopedia')
@section('content')
<h1 class="text-2xl font-bold">Checkout</h1>
<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <form method="POST" action="{{ route('checkout.store') }}" class="lg:col-span-2 rounded-2xl border bg-white p-6">
        @csrf
        <h2 class="font-bold">Alamat Pengiriman</h2>
        <textarea name="shipping_address" required rows="4" placeholder="Nama, jalan, kota, kode pos, no HP..." class="mt-3 w-full rounded-xl border px-4 py-3 text-sm">{{ old('shipping_address') }}</textarea>
        <button class="mt-4 w-full rounded-xl bg-black py-3 font-medium text-white hover:bg-gray-800">Buat Pesanan &mdash; Rp {{ number_format($total, 0, ',', '.') }}</button>
    </form>
    <div class="h-fit rounded-2xl border bg-white p-6">
        <h2 class="font-bold">Item ({{ $items->count() }})</h2>
        <div class="mt-3 space-y-2 text-sm">
            @foreach($items as $item)
            <div class="flex justify-between"><span>{{ $item['product']->name }} &times; {{ $item['quantity'] }}</span><span class="font-medium">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span></div>
            @endforeach
        </div>
        <div class="mt-4 border-t pt-3 flex justify-between font-bold"><span>Total</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
    </div>
</div>
@endsection
