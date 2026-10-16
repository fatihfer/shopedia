@extends('layouts.app')
@section('title', 'Profil Saya - Shopedia')
@section('content')
<div class="mx-auto max-w-xl">
    <div class="flex items-center gap-4">
        <span class="grid h-16 w-16 place-items-center rounded-full bg-night-950 text-2xl font-black text-cream-100 dark:bg-cream-100 dark:text-night-950">
            {{ mb_strtoupper(mb_substr($user->name ?? 'P', 0, 1)) }}
        </span>
        <div>
            <h1 class="text-2xl font-black tracking-tight">{{ $user->name }}</h1>
            <p class="text-sm text-cocoa-500 dark:text-sage-300">{{ $user->email }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-4 rounded-3xl border border-line/70 bg-card p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
        @csrf @method('PUT')
        <div>
            <label class="text-sm font-bold">Nama</label>
            <input name="name" value="{{ old('name', $user->name) }}" required maxlength="100"
                class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm outline-none focus:border-apricot-400 dark:focus:border-sage-500 dark:bg-cream-100/5 dark:border-cream-100/15 transition">
        </div>
        <div>
            <label class="text-sm font-bold">No. HP / WhatsApp</label>
            <input name="phone" value="{{ old('phone', $user->phone) }}" maxlength="20" placeholder="cth: 08123456789"
                class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm outline-none focus:border-apricot-400 dark:focus:border-sage-500 dark:bg-cream-100/5 dark:border-cream-100/15 transition">
        </div>
        <div>
            <label class="text-sm font-bold">Alamat utama</label>
            <textarea name="address" rows="3" maxlength="500" placeholder="Jalan, kota, kode pos... dipakai otomatis saat checkout"
                class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm leading-6 outline-none focus:border-apricot-400 dark:focus:border-sage-500 dark:bg-cream-100/5 dark:border-cream-100/15 transition">{{ old('address', $user->address) }}</textarea>
        </div>
        <button class="w-full rounded-2xl bg-night-950 py-3.5 font-bold text-cream-100 hover:bg-night-800 dark:bg-cream-100 dark:text-night-950 transition">Simpan Profil</button>
    </form>
</div>
@endsection
