@extends('layouts.app')
@section('title', ($product->exists ? 'Edit' : 'Tambah') . ' Produk')
@section('content')
<h1 class="text-2xl font-bold">{{ $product->exists ? 'Edit' : 'Tambah' }} Produk</h1>
<form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="mt-6 max-w-2xl space-y-4 rounded-2xl border bg-white p-6">
    @csrf
    @if($product->exists) @method('PUT') @endif
    <div>
        <label class="text-sm font-medium">Kategori</label>
        <select name="category_id" class="mt-1 w-full rounded-xl border px-4 py-2.5">
            @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-medium">Nama</label>
        <input name="name" value="{{ old('name', $product->name) }}" required class="mt-1 w-full rounded-xl border px-4 py-2.5">
    </div>
    <div>
        <label class="text-sm font-medium">Deskripsi</label>
        <textarea name="description" rows="4" class="mt-1 w-full rounded-xl border px-4 py-2.5">{{ old('description', $product->description) }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="text-sm font-medium">Harga (Rp)</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" required class="mt-1 w-full rounded-xl border px-4 py-2.5"></div>
        <div><label class="text-sm font-medium">Stok</label><input type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required class="mt-1 w-full rounded-xl border px-4 py-2.5"></div>
    </div>
    <div><label class="text-sm font-medium">Image path (storage/...)</label><input name="image_path" value="{{ old('image_path', $product->image_path) }}" placeholder="opsional" class="mt-1 w-full rounded-xl border px-4 py-2.5"></div>
    <button class="rounded-xl bg-black px-6 py-3 font-medium text-white">Simpan</button>
    <a href="{{ route('admin.products.index') }}" class="ml-2 text-sm text-gray-500">Batal</a>
</form>
@endsection
