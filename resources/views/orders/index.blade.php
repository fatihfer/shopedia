@extends('layouts.app')
@section('title', 'Pesanan Saya - Shopedia')
@section('content')
<h1 class="text-2xl font-bold">Pesanan Saya</h1>
<div class="mt-6 space-y-3">
@forelse($orders as $order)
    <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between rounded-2xl border bg-white p-5 hover:shadow">
        <div>
            <p class="font-bold">#{{ $order->id }} &mdash; Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500">{{ $order->items_count }} item &middot; {{ $order->created_at->format('d M Y H:i') }}</p>
        </div>
        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium uppercase">{{ $order->status }}</span>
    </a>
@empty
    <div class="rounded-2xl border bg-white p-10 text-center text-gray-500">Belum ada pesanan.</div>
@endforelse
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
