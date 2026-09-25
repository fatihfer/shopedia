@extends('layouts.app')
@section('title', 'Kelola Produk')
@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">Produk ({{ $products->total() }})</h1>
    <a href="{{ route('admin.products.create') }}" class="rounded-xl bg-black px-5 py-2.5 text-sm font-medium text-white">+ Produk</a>
</div>
<div class="mt-6 overflow-hidden rounded-2xl border bg-white">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500"><tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Kategori</th><th class="px-4 py-3">Harga</th><th class="px-4 py-3">Stok</th><th class="px-4 py-3"></th></tr></thead>
        <tbody>
        @foreach($products as $p)
        <tr class="border-t">
            <td class="px-4 py-3 font-medium">{{ $p->name }}<br><span class="text-xs text-gray-400">{{ $p->slug }}</span></td>
            <td class="px-4 py-3">{{ $p->category->name ?? '-' }}</td>
            <td class="px-4 py-3">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
            <td class="px-4 py-3">{{ $p->stock }}</td>
            <td class="px-4 py-3 text-right whitespace-nowrap">
                <a href="{{ route('admin.products.edit', $p) }}" class="text-blue-600">Edit</a>
                <form method="POST" action="{{ route('admin.products.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                    @csrf @method('DELETE')
                    <button class="ml-2 text-red-500">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
