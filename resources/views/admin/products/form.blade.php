@extends('layouts.app')
@section('title', ($product->exists ? 'Edit' : 'Tambah') . ' Produk')
@section('content')
<h1 class="flex items-center gap-2 text-2xl font-black tracking-tight"><x-icon name="box" class="h-6 w-6 text-ember-600 dark:text-sage-300" /> {{ $product->exists ? 'Edit' : 'Tambah' }} Produk</h1>
<form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data"
    class="mt-5 max-w-2xl space-y-4 rounded-3xl border border-line/70 bg-card p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
    @csrf
    @if($product->exists) @method('PUT') @endif
    <div>
        <label class="text-sm font-bold">Kategori</label>
        <select name="category_id" class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none focus:border-apricot-400 dark:focus:border-sage-500">
            @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-bold">Nama produk</label>
        <input name="name" value="{{ old('name', $product->name) }}" required placeholder="Contoh: Sneakers Premium X"
            class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none focus:border-apricot-400 dark:focus:border-sage-500">
    </div>
    <div>
        <label class="text-sm font-bold">Deskripsi</label>
        <textarea name="description" rows="4" placeholder="Deskripsi menarik..."
            class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none focus:border-apricot-400 dark:focus:border-sage-500">{{ old('description', $product->description) }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div><label class="text-sm font-bold">Harga (Rp)</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" required class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15"></div>
        <div><label class="text-sm font-bold">Stok</label><input type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15"></div>
    </div>
    <div>
        <label class="text-sm font-bold">Foto produk <span class="font-normal text-cocoa-500/70">(JPG/PNG/WebP, maks 2MB)</span></label>
        @if($product->image_path)
            <img src="{{ asset('storage/' . $product->image_path) }}" alt="Foto saat ini" class="mt-2 h-32 rounded-2xl border border-line dark:border-cream-100/15 object-cover">
            <p class="mt-1 text-xs text-cocoa-500/70">Upload baru untuk mengganti foto ini.</p>
        @endif
        <input type="file" name="image" accept="image/*"
            class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-2.5 text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-night-950 file:px-4 file:py-2 file:text-xs file:font-bold file:text-cream-100 dark:bg-cream-100/5 dark:border-cream-100/15">
    </div>
    <div class="flex items-center gap-3 pt-1">
        <button class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50 hover:opacity-90 transition"><x-icon name="check" class="h-4 w-4" /> Simpan</button>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-cocoa-500/70 hover:text-cocoa-700">Batal</a>
    </div>
</form>
@endsection
