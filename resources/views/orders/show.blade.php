@extends('layouts.app')
@section('title', 'Pesanan #' . $order->id)
@section('content')
<a href="{{ route('orders.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-sage-300 transition">← Semua pesanan</a>
<div class="mt-4 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-3xl border border-gray-200/70 bg-white p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-black">Pesanan #{{ $order->id }}</h1>
            <span class="rounded-full bg-gray-100 px-3.5 py-1.5 text-xs font-bold uppercase dark:bg-cream-100/10">{{ $order->status }}</span>
        </div>
        <p class="mt-1 text-sm text-gray-500 dark:text-sage-300">{{ $order->created_at->format('d M Y H:i') }}</p>
        {{-- Progress --}}
        @php $steps = ['pending','processing','shipped','completed']; $idx = array_search($order->status, $steps); @endphp
        @if($order->status !== 'cancelled')
        <div class="mt-5 flex items-center gap-1.5">
            @foreach($steps as $i => $s)
                <div class="flex-1 h-2 rounded-full {{ $idx !== false && $i <= $idx ? 'bg-gradient-to-r from-sage-400 to-sage-600' : 'bg-gray-200 dark:bg-cream-100/10' }}"></div>
            @endforeach
        </div>
        <p class="mt-2 text-xs text-gray-400">Menunggu → Diproses → Dikirim → Selesai</p>
        @endif
        <div class="mt-5 space-y-3">
            @foreach($order->items as $item)
            <div class="flex justify-between gap-3 border-b border-gray-100 pb-3 text-sm dark:border-cream-100/10">
                <div><p class="font-bold">{{ $item->product->name ?? 'Produk dihapus' }}</p><p class="text-gray-500">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p></div>
                <p class="font-extrabold whitespace-nowrap">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
        <div class="mt-4 flex justify-between font-black text-lg"><span>Total</span><span class="text-gradient">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>
    </div>
    <div class="h-fit rounded-3xl border border-gray-200/70 bg-white p-6 dark:bg-night-800 dark:border-cream-100/15">
        <h2 class="font-extrabold">📍 Pengiriman</h2>
        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-cream-100/80">{{ $order->shipping_address }}</p>
        <p class="mt-3 text-xs text-gray-400">Pemesan: {{ $order->user->name }} ({{ $order->user->email }})</p>
    </div>
</div>
@endsection
