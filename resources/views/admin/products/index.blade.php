@extends('layouts.app')
@section('title', 'Kelola Produk')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-black tracking-tight">Produk <span class="text-sm font-medium text-cocoa-500/70">({{ $products->total() }})</span></h1>
    <a href="{{ route('admin.products.create') }}" class="rounded-2xl bg-gradient-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50 hover:opacity-90 transition">+ Tambah Produk</a>
</div>
<div class="mt-5 overflow-hidden rounded-3xl border border-line/70 bg-card dark:bg-night-800 dark:border-cream-100/15">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[720px]">
        <thead class="bg-milk-100 text-left text-xs uppercase tracking-wider text-cocoa-500/70 dark:bg-cream-100/5"><tr><th class="px-5 py-3.5">Produk</th><th class="px-4 py-3.5">Kategori</th><th class="px-4 py-3.5">Harga</th><th class="px-4 py-3.5">Stok</th><th class="px-4 py-3.5 text-right">Aksi</th></tr></thead>
        <tbody class="divide-y divide-line/60 dark:divide-cream-100/10">
        @foreach($products as $p)
        <tr class="hover:bg-milk-100/70 dark:hover:bg-cream-100/5 transition">
            <td class="px-5 py-3.5"><p class="font-bold">{{ $p->name }}</p><p class="text-xs text-cocoa-500/70">{{ $p->slug }}</p></td>
            <td class="px-4 py-3.5"><span class="rounded-full bg-petal-100 px-2.5 py-1 text-xs font-semibold text-cocoa-700 dark:bg-sage-500/15 dark:text-sage-200">{{ $p->category->name ?? '-' }}</span></td>
            <td class="px-4 py-3.5 font-bold whitespace-nowrap">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
            <td class="px-4 py-3.5"><span class="font-bold {{ $p->stock < 5 ? 'text-red-500' : '' }}">{{ $p->stock }}</span></td>
            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                <a href="{{ route('products.show', $p) }}" class="text-cocoa-500/70 hover:text-cocoa-700 text-xs">Lihat</a>
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
