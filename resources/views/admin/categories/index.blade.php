@extends('layouts.app')
@section('title', 'Kelola Kategori')
@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-black tracking-tight">Kategori 📂</h1>
    <a href="{{ route('admin.categories.create') }}" class="rounded-2xl bg-gradient-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50 hover:opacity-90 transition">+ Kategori</a>
</div>
<div class="mt-5 overflow-hidden rounded-3xl border border-gray-200/70 bg-white dark:bg-night-800 dark:border-cream-100/15">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-400 dark:bg-cream-100/5"><tr><th class="px-5 py-3.5">Nama</th><th class="px-4 py-3.5">Slug</th><th class="px-4 py-3.5">Produk</th><th class="px-4 py-3.5 text-right">Aksi</th></tr></thead>
        <tbody class="divide-y divide-gray-100 dark:divide-cream-100/10">
        @foreach($categories as $c)
        <tr class="hover:bg-gray-50/70 dark:hover:bg-cream-100/5 transition">
            <td class="px-5 py-3.5 font-bold">📦 {{ $c->name }}</td>
            <td class="px-4 py-3.5 text-gray-400">{{ $c->slug }}</td>
            <td class="px-4 py-3.5"><span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold dark:bg-cream-100/10">{{ $c->products_count }}</span></td>
            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                <a href="{{ route('admin.categories.edit', $c) }}" class="rounded-lg bg-blue-50 px-2.5 py-1.5 text-xs font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">Edit</a>
                <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus?')">
                    @csrf @method('DELETE')
                    <button class="ml-1 rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-500 dark:bg-red-500/10">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $categories->links() }}</div>
@endsection
