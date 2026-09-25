@extends('layouts.app')
@section('title', 'Pesanan #' . $order->id)
@section('content')
<a href="{{ route('orders.index') }}" class="text-sm text-gray-500">&larr; Semua pesanan</a>
<div class="mt-4 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-2xl border bg-white p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold">Pesanan #{{ $order->id }}</h1>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium uppercase">{{ $order->status }}</span>
        </div>
        <p class="mt-1 text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</p>
        <div class="mt-4 space-y-3">
            @foreach($order->items as $item)
            <div class="flex justify-between border-b pb-3 text-sm">
                <div><p class="font-medium">{{ $item->product->name ?? 'Produk dihapus' }}</p><p class="text-gray-500">{{ $item->quantity }} &times; Rp {{ number_format($item->price, 0, ',', '.') }}</p></div>
                <p class="font-bold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
        <div class="mt-4 flex justify-between font-bold"><span>Total</span><span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>
    </div>
    <div class="h-fit rounded-2xl border bg-white p-6">
        <h2 class="font-bold">Pengiriman</h2>
        <p class="mt-2 text-sm text-gray-600">{{ $order->shipping_address }}</p>
        <p class="mt-3 text-sm text-gray-500">Pemesan: {{ $order->user->name }} ({{ $order->user->email }})</p>
    </div>
</div>
@endsection
