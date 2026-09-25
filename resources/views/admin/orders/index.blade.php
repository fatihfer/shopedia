@extends('layouts.app')
@section('title', 'Kelola Pesanan')
@section('content')
<h1 class="text-2xl font-bold">Semua Pesanan</h1>
<div class="mt-6 overflow-hidden rounded-2xl border bg-white">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500"><tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Tanggal</th></tr></thead>
        <tbody>
        @foreach($orders as $o)
        <tr class="border-t hover:bg-gray-50">
            <td class="px-4 py-3"><a href="{{ route('admin.orders.show', $o) }}" class="font-bold text-blue-600">#{{ $o->id }}</a></td>
            <td class="px-4 py-3">{{ $o->user->name ?? '-' }}</td>
            <td class="px-4 py-3">Rp {{ number_format($o->total_price, 0, ',', '.') }}</td>
            <td class="px-4 py-3 uppercase text-xs">{{ $o->status }}</td>
            <td class="px-4 py-3 text-gray-500">{{ $o->created_at->format('d M Y H:i') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
