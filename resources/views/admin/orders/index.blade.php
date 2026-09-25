@extends('layouts.app')
@section('title', 'Kelola Pesanan')
@section('content')
<h1 class="text-2xl font-black tracking-tight">Semua Pesanan 🧾</h1>
<p class="text-sm text-gray-500 dark:text-gray-400">Klik ID untuk detail & update status.</p>
<div class="mt-5 overflow-hidden rounded-3xl border border-gray-200/70 bg-white dark:bg-gray-900 dark:border-white/10">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[680px]">
        <thead class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-400 dark:bg-white/5"><tr><th class="px-5 py-3.5">ID</th><th class="px-4 py-3.5">Customer</th><th class="px-4 py-3.5">Total</th><th class="px-4 py-3.5">Status</th><th class="px-4 py-3.5">Tanggal</th></tr></thead>
        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
        @foreach($orders as $o)
        <tr class="hover:bg-gray-50/70 dark:hover:bg-white/5 transition">
            <td class="px-5 py-3.5"><a href="{{ route('admin.orders.show', $o) }}" class="font-black text-indigo-600 dark:text-indigo-400">#{{ $o->id }}</a></td>
            <td class="px-4 py-3.5">{{ $o->user->name ?? '-' }}</td>
            <td class="px-4 py-3.5 font-bold whitespace-nowrap">Rp {{ number_format($o->total_price, 0, ',', '.') }}</td>
            <td class="px-4 py-3.5"><span class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-bold uppercase dark:bg-white/10">{{ $o->status }}</span></td>
            <td class="px-4 py-3.5 text-gray-400">{{ $o->created_at->format('d M Y H:i') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
