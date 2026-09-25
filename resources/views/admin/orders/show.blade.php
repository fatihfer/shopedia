@extends('layouts.app')
@section('title', 'Pesanan #' . $order->id)
@section('content')
<a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500">&larr; Semua pesanan</a>
<div class="mt-4 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-2xl border bg-white p-6">
        <h1 class="text-xl font-bold">Pesanan #{{ $order->id }}</h1>
        <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }} &middot; {{ $order->user->name }} ({{ $order->user->email }})</p>
        <div class="mt-4 space-y-2 text-sm">
            @foreach($order->items as $item)
            <div class="flex justify-between border-b pb-2"><span>{{ $item->product->name ?? '-' }} &times; {{ $item->quantity }}</span><span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span></div>
            @endforeach
        </div>
        <p class="mt-4 flex justify-between font-bold"><span>Total</span><span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></p>
        <p class="mt-4 text-sm"><span class="font-medium">Alamat:</span> {{ $order->shipping_address }}</p>
    </div>
    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="h-fit rounded-2xl border bg-white p-6">
        @csrf @method('PATCH')
        <h2 class="font-bold">Update Status</h2>
        <select name="status" class="mt-3 w-full rounded-xl border px-4 py-2.5">
            @foreach(['pending','processing','shipped','completed','cancelled'] as $s)
            <option value="{{ $s }}" @selected($order->status === $s)>{{ $s }}</option>
            @endforeach
        </select>
        <button class="mt-3 w-full rounded-xl bg-black py-2.5 text-sm font-medium text-white">Simpan</button>
    </form>
</div>
@endsection
