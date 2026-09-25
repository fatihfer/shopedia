@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<h1 class="text-2xl font-bold">Dashboard Admin</h1>
<div class="mt-4 flex gap-3 text-sm">
    <a href="{{ route('admin.products.index') }}" class="rounded-lg border bg-white px-4 py-2">Produk</a>
    <a href="{{ route('admin.categories.index') }}" class="rounded-lg border bg-white px-4 py-2">Kategori</a>
    <a href="{{ route('admin.orders.index') }}" class="rounded-lg border bg-white px-4 py-2">Pesanan</a>
    <a href="{{ route('home') }}" class="rounded-lg border bg-white px-4 py-2">Lihat Toko</a>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
    <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-gray-500">Omzet</p><p class="text-xl font-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p></div>
    <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-gray-500">Total Order</p><p class="text-xl font-bold">{{ $totalOrders }}</p></div>
    <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-gray-500">Pending</p><p class="text-xl font-bold">{{ $pendingOrders }}</p></div>
    <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-gray-500">Produk</p><p class="text-xl font-bold">{{ $totalProducts }}</p></div>
    <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-gray-500">Customer</p><p class="text-xl font-bold">{{ $totalCustomers }}</p></div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="rounded-2xl border bg-white p-6">
        <h2 class="font-bold">Pesanan Terbaru</h2>
        <div class="mt-3 space-y-2 text-sm">
            @forelse($recentOrders as $o)
            <a href="{{ route('admin.orders.show', $o) }}" class="flex justify-between border-b pb-2 hover:text-blue-600">
                <span>#{{ $o->id }} &middot; {{ $o->user->name ?? '-' }} &middot; Rp {{ number_format($o->total_price, 0, ',', '.') }}</span>
                <span class="uppercase text-gray-400">{{ $o->status }}</span>
            </a>
            @empty <p class="text-gray-500">Belum ada order.</p> @endforelse
        </div>
    </div>
    <div class="rounded-2xl border bg-white p-6">
        <h2 class="font-bold">Stok Menipis</h2>
        <div class="mt-3 space-y-2 text-sm">
            @foreach($lowStock as $p)
            <div class="flex justify-between border-b pb-2"><span>{{ $p->name }}</span><span class="{{ $p->stock < 5 ? 'text-red-500 font-bold' : 'text-gray-500' }}">{{ $p->stock }}</span></div>
            @endforeach
        </div>
    </div>
</div>
@endsection
