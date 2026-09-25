@extends('layouts.app')
@section('title', ($category->exists ? 'Edit' : 'Tambah') . ' Kategori')
@section('content')
<h1 class="text-2xl font-bold">{{ $category->exists ? 'Edit' : 'Tambah' }} Kategori</h1>
<form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="mt-6 max-w-lg space-y-4 rounded-2xl border bg-white p-6">
    @csrf
    @if($category->exists) @method('PUT') @endif
    <div><label class="text-sm font-medium">Nama</label><input name="name" value="{{ old('name', $category->name) }}" required class="mt-1 w-full rounded-xl border px-4 py-2.5"></div>
    <button class="rounded-xl bg-black px-6 py-3 font-medium text-white">Simpan</button>
    <a href="{{ route('admin.categories.index') }}" class="ml-2 text-sm text-gray-500">Batal</a>
</form>
@endsection
