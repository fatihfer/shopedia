@extends('layouts.app')
@section('title', 'Kelola Kategori')
@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">Kategori</h1>
    <a href="{{ route('admin.categories.create') }}" class="rounded-xl bg-black px-5 py-2.5 text-sm font-medium text-white">+ Kategori</a>
</div>
<div class="mt-6 overflow-hidden rounded-2xl border bg-white">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500"><tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Slug</th><th class="px-4 py-3">Produk</th><th class="px-4 py-3"></th></tr></thead>
        <tbody>
        @foreach($categories as $c)
        <tr class="border-t">
            <td class="px-4 py-3 font-medium">{{ $c->name }}</td>
            <td class="px-4 py-3 text-gray-500">{{ $c->slug }}</td>
            <td class="px-4 py-3">{{ $c->products_count }}</td>
            <td class="px-4 py-3 text-right">
                <a href="{{ route('admin.categories.edit', $c) }}" class="text-blue-600">Edit</a>
                <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus?')">
                    @csrf @method('DELETE')
                    <button class="ml-2 text-red-500">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $categories->links() }}</div>
@endsection
