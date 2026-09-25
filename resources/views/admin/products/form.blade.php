@extends('layouts.app')
@section('title', ($product->exists ? 'Edit' : 'Tambah') . ' Produk')
@section('content')
<h1 class="text-2xl font-black tracking-tight">{{ $product->exists ? '✏️ Edit' : '➕ Tambah' }} Produk</h1>
<form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
    class="mt-5 max-w-2xl space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6 sm:p-8 dark:bg-gray-900 dark:border-white/10">
    @csrf
    @if($product->exists) @method('PUT') @endif
    <div>
        <label class="text-sm font-bold">Kategori</label>
        <select name="category_id" class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:bg-white/5 dark:border-white/10 outline-none focus:border-indigo-400">
            @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-bold">Nama produk</label>
        <input name="name" value="{{ old('name', $product->name) }}" required placeholder="Contoh: Sneakers Premium X"
            class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:bg-white/5 dark:border-white/10 outline-none focus:border-indigo-400">
    </div>
    <div>
        <label class="text-sm font-bold">Deskripsi</label>
        <textarea name="description" rows="4" placeholder="Deskripsi menarik..."
            class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:bg-white/5 dark:border-white/10 outline-none focus:border-indigo-400">{{ old('description', $product->description) }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div><label class="text-sm font-bold">Harga (Rp)</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" required class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:bg-white/5 dark:border-white/10"></div>
        <div><label class="text-sm font-bold">Stok</label><input type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:bg-white/5 dark:border-white/10"></div>
    </div>
    <div><label class="text-sm font-bold">Image path <span class="font-normal text-gray-400">(opsional, storage/...)</span></label><input name="image_path" value="{{ old('image_path', $product->image_path) }}" class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:bg-white/5 dark:border-white/10"></div>
    <div class="flex items-center gap-3 pt-1">
        <button class="rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/25 hover:opacity-90 transition">💾 Simpan</button>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-400 hover:text-gray-600">Batal</a>
    </div>
</form>
@endsection
