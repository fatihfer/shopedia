@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Dashboard ✨</h1>
        <p class="text-sm text-gray-500 dark:text-sage-300">Ringkasan performa tokomu hari ini.</p>
    </div>
    <div class="flex gap-2 text-sm font-medium">
        <a href="{{ route('admin.products.index') }}" class="rounded-full border border-gray-200 bg-white px-4 py-2 hover:bg-gray-100 dark:bg-cream-100/5 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition">📦 Produk</a>
        <a href="{{ route('admin.categories.index') }}" class="rounded-full border border-gray-200 bg-white px-4 py-2 hover:bg-gray-100 dark:bg-cream-100/5 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition">📂 Kategori</a>
        <a href="{{ route('admin.orders.index') }}" class="rounded-full bg-gray-900 px-4 py-2 text-white hover:bg-gray-700 dark:bg-cream-100 dark:text-night-950 transition">🧾 Pesanan</a>
    </div>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
    @php
    $cards = [
        ['Omzet Bersih', 'Rp ' . number_format($totalRevenue, 0, ',', '.'), '💰', 'from-emerald-500 to-teal-500'],
        ['Total Order', $totalOrders, '🧾', 'from-sage-500 to-night-700'],
        ['Pending', $pendingOrders, '⏳', 'from-amber-500 to-orange-500'],
        ['Produk', $totalProducts, '📦', 'from-sky-500 to-blue-500'],
        ['Customer', $totalCustomers, '👥', 'from-fuchsia-500 to-pink-500'],
    ];
    @endphp
    @foreach($cards as [$label, $value, $icon, $grad])
    <div class="rounded-3xl border border-gray-200/70 bg-white p-5 dark:bg-night-800 dark:border-cream-100/15 card-lift">
        <span class="grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br {{ $grad }} text-lg text-white shadow">{{ $icon }}</span>
        <p class="mt-3 text-xs font-bold uppercase tracking-wider text-gray-400">{{ $label }}</p>
        <p class="text-lg font-black truncate">{{ $value }}</p>
    </div>
    @endforeach
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="rounded-3xl border border-gray-200/70 bg-white p-6 dark:bg-night-800 dark:border-cream-100/15">
        <div class="flex items-center justify-between"><h2 class="font-extrabold">Pesanan Terbaru</h2><a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-indigo-600 dark:text-sage-300">Lihat semua →</a></div>
        <div class="mt-3 space-y-1 text-sm">
            @forelse($recentOrders as $o)
            <a href="{{ route('admin.orders.show', $o) }}" class="flex justify-between gap-3 rounded-2xl px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-cream-100/5 transition">
                <span class="truncate"><span class="font-extrabold">#{{ $o->id }}</span> · {{ $o->user->name ?? '-' }} · <span class="font-semibold">Rp {{ number_format($o->total_price, 0, ',', '.') }}</span></span>
                <span class="shrink-0 uppercase text-xs text-gray-400">{{ $o->status }}</span>
            </a>
            @empty <p class="text-gray-500 text-sm">Belum ada order.</p> @endforelse
        </div>
    </div>
    <div class="rounded-3xl border border-gray-200/70 bg-white p-6 dark:bg-night-800 dark:border-cream-100/15">
        <h2 class="font-extrabold">⚠️ Stok Menipis</h2>
        <div class="mt-3 space-y-1 text-sm">
            @foreach($lowStock as $p)
            <div class="flex justify-between gap-3 rounded-2xl px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-cream-100/5">
                <span class="truncate font-medium">{{ $p->name }}</span>
                <span class="font-black {{ $p->stock < 5 ? 'text-red-500' : 'text-gray-400' }}">{{ $p->stock }} pcs</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
