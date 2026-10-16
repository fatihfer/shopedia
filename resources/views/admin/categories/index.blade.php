@extends('layouts.admin')
@section('title', 'Kelola Kategori')
@section('header', 'Kategori')
@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-night-950 px-4 py-2.5 text-sm font-bold text-cream-100 hover:bg-night-800 dark:bg-cream-100 dark:text-night-950 transition"><x-icon name="plus" class="h-4 w-4" /> Kategori</a>
</div>
<div class="mt-5 overflow-hidden rounded-3xl border border-line/70 bg-card dark:bg-night-800 dark:border-cream-100/15">
    <table class="w-full text-sm">
        <thead class="bg-milk-100 text-left text-xs uppercase tracking-wider text-cocoa-500/70 dark:bg-cream-100/5"><tr><th class="px-5 py-3.5">Nama</th><th class="px-4 py-3.5">Slug</th><th class="px-4 py-3.5">Produk</th><th class="px-4 py-3.5 text-right">Aksi</th></tr></thead>
        <tbody class="divide-y divide-line/60 dark:divide-cream-100/10">
        @foreach($categories as $c)
        <tr class="hover:bg-milk-100/70 dark:hover:bg-cream-100/5 transition">
            <td class="px-5 py-3.5 font-bold"><span class="inline-flex items-center gap-2"><x-icon name="tag" class="h-4 w-4 text-cocoa-500/60" /> {{ $c->name }}</span></td>
            <td class="px-4 py-3.5 text-cocoa-500/70">{{ $c->slug }}</td>
            <td class="px-4 py-3.5"><span class="rounded-full bg-sand-200/60 px-2.5 py-1 text-xs font-bold dark:bg-cream-100/10">{{ $c->products_count }}</span></td>
            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                <a href="{{ route('admin.categories.edit', $c) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1.5 text-xs font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-300"><x-icon name="pencil" class="h-3.5 w-3.5" /> Edit</a>
                <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus?')">
                    @csrf @method('DELETE')
                    <button class="ml-1 inline-flex items-center gap-1 rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-500 dark:bg-red-500/10"><x-icon name="trash" class="h-3.5 w-3.5" /> Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $categories->links() }}</div>
@endsection
