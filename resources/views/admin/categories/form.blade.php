@extends('layouts.app')
@section('title', ($category->exists ? 'Edit' : 'Tambah') . ' Kategori')
@section('content')
<h1 class="text-2xl font-black tracking-tight">{{ $category->exists ? '✏️ Edit' : '➕ Tambah' }} Kategori</h1>
<form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
    class="mt-5 max-w-lg space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
    @csrf
    @if($category->exists) @method('PUT') @endif
    <div><label class="text-sm font-bold">Nama kategori</label><input name="name" value="{{ old('name', $category->name) }}" required placeholder="Contoh: Sneakers"
        class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none focus:border-indigo-400 dark:focus:border-sage-500"></div>
    <div class="flex items-center gap-3">
        <button class="rounded-2xl bg-gradient-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50">💾 Simpan</button>
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-400">Batal</a>
    </div>
</form>
@endsection
