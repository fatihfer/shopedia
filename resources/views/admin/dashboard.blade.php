@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="flex items-center gap-2.5 text-2xl sm:text-3xl font-black tracking-tight">Dashboard <x-icon name="sparkles" class="h-7 w-7 text-ember-600 dark:text-sage-300" /></h1>
        <p class="text-sm text-cocoa-500 dark:text-sage-300">Ringkasan performa tokomu hari ini.</p>
    </div>
    <div class="flex gap-2 text-sm font-medium">
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 rounded-full border border-line bg-card px-4 py-2 hover:bg-sand-200 dark:bg-cream-100/5 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition"><x-icon name="box" class="h-4 w-4" /> Produk</a>
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-1.5 rounded-full border border-line bg-card px-4 py-2 hover:bg-sand-200 dark:bg-cream-100/5 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition"><x-icon name="folder" class="h-4 w-4" /> Kategori</a>
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 rounded-full border border-line bg-card px-4 py-2 hover:bg-sand-200 dark:bg-cream-100/5 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition"><x-icon name="receipt" class="h-4 w-4" /> Pesanan</a>
        <a href="{{ route('admin.payment-methods.index') }}" class="inline-flex items-center gap-1.5 rounded-full border border-line bg-card px-4 py-2 hover:bg-sand-200 dark:bg-cream-100/5 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition"><x-icon name="card" class="h-4 w-4" /> Bayar</a>
    </div>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
    @php
    $cards = [
        ['Omzet Bersih', 'Rp ' . number_format($totalRevenue, 0, ',', '.'), 'money', 'from-emerald-500 to-teal-500'],
        ['Total Order', $totalOrders, 'receipt', 'from-sage-500 to-night-700'],
        ['Pending', $pendingOrders, 'clock', 'from-amber-500 to-orange-500'],
        ['Produk', $totalProducts, 'box', 'from-sky-500 to-blue-500'],
        ['Customer', $totalCustomers, 'users', 'from-fuchsia-500 to-pink-500'],
    ];
    @endphp
    @foreach($cards as [$label, $value, $icon, $grad])
    <div class="rounded-3xl border border-line/70 bg-card p-5 dark:bg-night-800 dark:border-cream-100/15 card-lift">
        <span class="grid h-10 w-10 place-items-center rounded-2xl bg-linear-to-br {{ $grad }} text-white shadow"><x-icon :name="$icon" class="h-5 w-5" /></span>
        <p class="mt-3 text-xs font-bold uppercase tracking-wider text-cocoa-500/70">{{ $label }}</p>
        <p class="text-lg font-black truncate">{{ $value }}</p>
    </div>
    @endforeach
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="rounded-3xl border border-line/70 bg-card p-6 dark:bg-night-800 dark:border-cream-100/15">
        <div class="flex items-center justify-between"><h2 class="font-extrabold">Pesanan Terbaru</h2><a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-cocoa-700 dark:text-sage-300">Lihat semua <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a></div>
        <div class="mt-3 space-y-1 text-sm">
            @forelse($recentOrders as $o)
            <a href="{{ route('admin.orders.show', $o) }}" class="flex justify-between gap-3 rounded-2xl px-3 py-2.5 hover:bg-milk-100 dark:hover:bg-cream-100/5 transition">
                <span class="truncate"><span class="font-extrabold">#{{ $o->id }}</span> · {{ $o->user->name ?? '-' }} · <span class="font-semibold">Rp {{ number_format($o->total_price, 0, ',', '.') }}</span></span>
                <span class="shrink-0 uppercase text-xs text-cocoa-500/70">{{ $o->status }}</span>
            </a>
            @empty <p class="text-cocoa-500 text-sm">Belum ada order.</p> @endforelse
        </div>
    </div>
    <div class="rounded-3xl border border-line/70 bg-card p-6 dark:bg-night-800 dark:border-cream-100/15">
        <h2 class="flex items-center gap-2 font-extrabold"><x-icon name="alert" class="h-5 w-5 text-amber-500" /> Stok Menipis</h2>
        <div class="mt-3 space-y-1 text-sm">
            @foreach($lowStock as $p)
            <div class="flex justify-between gap-3 rounded-2xl px-3 py-2.5 hover:bg-milk-100 dark:hover:bg-cream-100/5">
                <span class="truncate font-medium">{{ $p->name }}</span>
                <span class="font-black {{ $p->stock < 5 ? 'text-red-500' : 'text-cocoa-500/70' }}">{{ $p->stock }} pcs</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
