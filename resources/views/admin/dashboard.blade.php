@extends('layouts.admin')
@section('title', 'Dashboard')
@section('header', 'Overview')
@section('content')

{{-- Stat cards --}}
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @php
    $stats = [
        ['Omzet', 'Rp ' . number_format($totalRevenue, 0, ',', '.'), '30 hari: Rp ' . number_format($revenue30, 0, ',', '.'), 'money', 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300'],
        ['Total Order', $totalOrders, $orders30 . ' order 30 hari terakhir', 'receipt', 'bg-sky-100 text-sky-600 dark:bg-sky-500/10 dark:text-sky-300'],
        ['Customer', $totalCustomers, 'akun terdaftar', 'users', 'bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-300'],
        ['Perlu Verifikasi', $pendingOrders, 'pembayaran menunggu', 'clock', 'bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300'],
    ];
    @endphp
    @foreach($stats as [$label, $value, $sub, $icon, $chip])
    <div class="rounded-2xl bg-white p-5 shadow-sm dark:bg-night-800">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-sage-300">{{ $label }}</p>
                <p class="mt-0.5 text-xs text-gray-400">{{ $sub }}</p>
            </div>
            <span class="grid h-10 w-10 place-items-center rounded-full {{ $chip }}"><x-icon :name="$icon" class="h-5 w-5" /></span>
        </div>
        <p class="mt-3 text-2xl font-black tracking-tight tabular-nums">{{ $value }}</p>
    </div>
    @endforeach
</div>

<div class="mt-5 grid gap-5 lg:grid-cols-3">
    {{-- Sales chart --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2 dark:bg-night-800">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold">Grafik Penjualan</h2>
            <span class="text-xs text-gray-400">7 hari terakhir · omzet lunas</span>
        </div>
        <div class="mt-5 flex h-44 items-end gap-2 sm:gap-3">
            @foreach($week as $day)
            <div class="flex flex-1 flex-col items-center gap-1.5">
                <span class="text-[10px] font-bold tabular-nums text-gray-400">{{ $day['revenue'] > 0 ? number_format($day['revenue'] / 1000, 0) . 'rb' : '' }}</span>
                <div class="w-full rounded-t-lg bg-teal-200 dark:bg-sage-600" style="height: {{ max(4, $day['revenue'] / $maxRevenue * 100) }}%" title="Rp {{ number_format($day['revenue'], 0, ',', '.') }} · {{ $day['orders'] }} order"></div>
                <span class="text-[10px] text-gray-400">{{ $day['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Perlu tindakan --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm dark:bg-night-800">
        <h2 class="font-extrabold">Perlu Tindakan</h2>
        <div class="mt-4 space-y-3 text-sm">
            <a href="{{ route('admin.orders.index', ['status' => 'awaiting_verification']) }}" class="flex items-center justify-between rounded-xl bg-amber-50 px-4 py-3 hover:bg-amber-100 dark:bg-amber-500/10 transition">
                <span class="font-bold text-amber-700 dark:text-amber-300">Verifikasi pembayaran</span>
                <span class="rounded-full bg-amber-500 px-2.5 py-0.5 text-xs font-black text-white">{{ $pendingOrders }}</span>
            </a>
            <div class="rounded-xl bg-gray-50 px-4 py-3 dark:bg-cream-100/5">
                <p class="font-bold">Stok menipis</p>
                <ul class="mt-1.5 space-y-1 text-xs text-gray-500 dark:text-sage-300">
                    @forelse($lowStock->take(4) as $p)
                    <li class="flex justify-between"><span class="truncate">{{ $p->name }}</span><span class="font-black {{ $p->stock < 5 ? 'text-red-500' : '' }}">{{ $p->stock }}</span></li>
                    @empty
                    <li>Semua stok aman.</li>
                    @endforelse
                </ul>
            </div>
            <a href="{{ route('admin.products.create') }}" class="flex items-center justify-center gap-1.5 rounded-xl bg-night-950 px-4 py-2.5 text-sm font-bold text-cream-100 hover:bg-night-800 dark:bg-cream-100 dark:text-night-950 transition">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Produk
            </a>
        </div>
    </div>
</div>

<div class="mt-5 grid gap-5 lg:grid-cols-3">
    {{-- Top products --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2 dark:bg-night-800">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold">Produk Terlaris</h2>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-gray-400 hover:text-gray-600">Semua <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a>
        </div>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($topProducts as $item)
            <div class="overflow-hidden rounded-2xl border border-gray-100 dark:border-cream-100/10">
                <div class="flex aspect-[4/3] items-center justify-center bg-gray-50 dark:bg-cream-100/5">
                    @if($item->product?->image_path)
                        <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="" class="h-full w-full object-cover">
                    @else
                        <x-icon name="bag" class="h-10 w-10 text-gray-300 dark:text-sage-500" />
                    @endif
                </div>
                <div class="p-3">
                    <p class="line-clamp-1 text-sm font-bold">{{ $item->product->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $item->sold }} terjual</p>
                </div>
            </div>
            @empty
            <p class="col-span-full rounded-2xl bg-gray-50 p-8 text-center text-sm text-gray-400 dark:bg-cream-100/5">Belum ada penjualan tercatat.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent orders --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm dark:bg-night-800">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold">Order Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-gray-400 hover:text-gray-600">Semua <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a>
        </div>
        <div class="mt-3 space-y-1 text-sm">
            @forelse($recentOrders as $o)
            <a href="{{ route('admin.orders.show', $o) }}" class="flex items-center justify-between gap-2 rounded-xl px-3 py-2 hover:bg-gray-50 dark:hover:bg-cream-100/5 transition">
                <span class="min-w-0"><span class="font-extrabold">#{{ $o->id }}</span> <span class="text-gray-400">· {{ $o->user->name ?? '-' }}</span><br><span class="text-xs font-semibold">Rp {{ number_format($o->total_price, 0, ',', '.') }}</span></span>
                <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-[10px] font-bold uppercase dark:bg-cream-100/10">{{ str_replace('_', ' ', $o->status) }}</span>
            </a>
            @empty
            <p class="text-sm text-gray-400">Belum ada order.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
