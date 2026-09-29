@extends('layouts.app')
@section('title', 'Pesanan #' . $order->id)
@section('content')
<a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-sage-300 transition">← Semua pesanan</a>
<div class="mt-4 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-3xl border border-gray-200/70 bg-white p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
        <h1 class="text-xl font-black">Pesanan #{{ $order->id }}</h1>
        <p class="text-sm text-gray-500 dark:text-sage-300">{{ $order->created_at->format('d M Y H:i') }} · {{ $order->user->name }} ({{ $order->user->email }})</p>
        <div class="mt-4 space-y-2 text-sm">
            @foreach($order->items as $item)
            <div class="flex justify-between gap-3 rounded-2xl bg-gray-50 p-3 dark:bg-cream-100/5"><span class="truncate">{{ $item->product->name ?? '-' }} × {{ $item->quantity }}</span><span class="font-bold whitespace-nowrap">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span></div>
            @endforeach
        </div>
        <p class="mt-4 flex justify-between font-black text-lg"><span>Total</span><span class="text-gradient">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></p>
        <p class="mt-4 text-sm rounded-2xl bg-gray-50 p-4 dark:bg-cream-100/5"><span class="font-bold">📍 Alamat:</span> {{ $order->shipping_address }}</p>
    </div>
    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="h-fit rounded-3xl border border-gray-200/70 bg-white p-6 dark:bg-night-800 dark:border-cream-100/15">
        @csrf @method('PATCH')
        <h2 class="font-extrabold">🔄 Update Status</h2>
        <p class="text-xs text-gray-400 mt-1">Status saat ini: <span class="font-bold uppercase">{{ $order->status }}</span></p>
        <select name="status" class="mt-3 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none focus:border-indigo-400 dark:focus:border-sage-500">
            @foreach(['pending','processing','shipped','completed','cancelled'] as $s)
            <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button class="mt-3 w-full rounded-2xl bg-gray-900 py-3 text-sm font-bold text-white hover:bg-gray-700 dark:bg-cream-100 dark:text-night-950 transition">Simpan Status</button>
    </form>
</div>
@endsection
