@extends('layouts.app')
@section('title', 'Kelola Produk')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-black tracking-tight">Produk <span class="text-sm font-medium text-gray-400">({{ $products->total() }})</span></h1>
    <a href="{{ route('admin.products.create') }}" class="rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 hover:opacity-90 transition">+ Tambah Produk</a>
</div>
<div class="mt-5 overflow-hidden rounded-3xl border border-gray-200/70 bg-white dark:bg-gray-900 dark:border-white/10">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[720px]">
        <thead class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-400 dark:bg-white/5"><tr><th class="px-5 py-3.5">Produk</th><th class="px-4 py-3.5">Kategori</th><th class="px-4 py-3.5">Harga</th><th class="px-4 py-3.5">Stok</th><th class="px-4 py-3.5 text-right">Aksi</th></tr></thead>
        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
        @foreach($products as $p)
        <tr class="hover:bg-gray-50/70 dark:hover:bg-white/5 transition">
            <td class="px-5 py-3.5"><p class="font-bold">{{ $p->name }}</p><p class="text-xs text-gray-400">{{ $p->slug }}</p></td>
            <td class="px-4 py-3.5"><span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300">{{ $p->category->name ?? '-' }}</span></td>
            <td class="px-4 py-3.5 font-bold whitespace-nowrap">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
            <td class="px-4 py-3.5"><span class="font-bold {{ $p->stock < 5 ? 'text-red-500' : '' }}">{{ $p->stock }}</span></td>
            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                <a href="{{ route('products.show', $p) }}" class="text-gray-400 hover:text-gray-600 text-xs">Lihat</a>
                <a href="{{ route('admin.products.edit', $p) }}" class="ml-2 rounded-lg bg-blue-50 px-2.5 py-1.5 text-xs font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">Edit</a>
                <form method="POST" action="{{ route('admin.products.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                    @csrf @method('DELETE')
                    <button class="ml-1 rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-500 dark:bg-red-500/10">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
