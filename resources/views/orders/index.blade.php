@extends('layouts.app')
@section('title', 'Pesanan Saya - Shopedia')
@section('content')
<h1 class="text-2xl sm:text-3xl font-black tracking-tight">Pesanan Saya 📦</h1>
<p class="text-sm text-gray-500 dark:text-gray-400">Pantau status belanjamu di sini.</p>
@php
$statusStyle = [
    'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
    'processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300',
    'shipped' => 'bg-violet-100 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300',
    'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
    'cancelled' => 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-300',
];
@endphp
<div class="mt-6 space-y-3">
@forelse($orders as $order)
    <a href="{{ route('orders.show', $order) }}" class="card-lift flex items-center justify-between gap-4 rounded-3xl border border-gray-200/70 bg-white p-5 dark:bg-gray-900 dark:border-white/10">
        <div class="flex items-center gap-4 min-w-0">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-indigo-100 to-fuchsia-100 text-xl dark:from-white/10 dark:to-white/5">📦</span>
            <div class="min-w-0">
                <p class="font-extrabold">#{{ $order->id }} · Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->items_count }} item · {{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
        <span class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-bold uppercase tracking-wide {{ $statusStyle[$order->status] ?? 'bg-gray-100 text-gray-500' }}">{{ $order->status }}</span>
    </a>
@empty
    <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-14 text-center dark:bg-gray-900 dark:border-white/10">
        <p class="text-5xl">📭</p>
        <p class="mt-3 font-extrabold">Belum ada pesanan</p>
        <a href="{{ route('products.index') }}" class="mt-4 inline-block rounded-2xl bg-gray-900 px-6 py-3 text-sm font-bold text-white dark:bg-white dark:text-gray-900">Belanja dulu →</a>
    </div>
@endforelse
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
