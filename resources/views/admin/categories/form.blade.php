@extends('layouts.app')
@section('title', ($category->exists ? 'Edit' : 'Tambah') . ' Kategori')
@section('content')
<h1 class="flex items-center gap-2 text-2xl font-black tracking-tight"><x-icon name="folder" class="h-6 w-6 text-ember-600 dark:text-sage-300" /> {{ $category->exists ? 'Edit' : 'Tambah' }} Kategori</h1>
<form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
    class="mt-5 max-w-lg space-y-4 rounded-3xl border border-line/70 bg-card p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
    @csrf
    @if($category->exists) @method('PUT') @endif
    <div><label class="text-sm font-bold">Nama kategori</label><input name="name" value="{{ old('name', $category->name) }}" required placeholder="Contoh: Sneakers"
        class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none focus:border-apricot-400 dark:focus:border-sage-500"></div>
    <div class="flex items-center gap-3">
        <button class="inline-flex items-center gap-2 rounded-2xl bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50"><x-icon name="check" class="h-4 w-4" /> Simpan</button>
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-cocoa-500/70">Batal</a>
    </div>
</form>
@endsection
