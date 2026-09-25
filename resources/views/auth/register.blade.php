@extends('layouts.app')
@section('title', 'Daftar - Shopedia')
@section('content')
<div class="mx-auto max-w-md">
    <div class="overflow-hidden rounded-3xl border border-gray-200/70 bg-white dark:bg-gray-900 dark:border-white/10 shadow-xl shadow-indigo-500/5">
        <div class="bg-gradient-to-r from-fuchsia-600 to-indigo-600 p-7 text-white">
            <p class="text-3xl">🎉</p>
            <h1 class="mt-2 text-2xl font-black tracking-tight">Buat akun gratis</h1>
            <p class="text-sm text-white/75">Checkout lebih cepat & pantau pesanan.</p>
        </div>
        <form method="POST" action="{{ route('register') }}" class="p-7 space-y-4">
            @csrf
            <div>
                <label class="text-sm font-bold">Nama lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama kamu"
                    class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 dark:bg-white/5 dark:border-white/10 transition">
            </div>
            <div>
                <label class="text-sm font-bold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="kamu@email.com"
                    class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 dark:bg-white/5 dark:border-white/10 transition">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-bold">Password</label>
                    <input type="password" name="password" required placeholder="Min. 8"
                        class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-indigo-400 dark:bg-white/5 dark:border-white/10 transition">
                </div>
                <div>
                    <label class="text-sm font-bold">Konfirmasi</label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi"
                        class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-indigo-400 dark:bg-white/5 dark:border-white/10 transition">
                </div>
            </div>
            <button class="w-full rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 py-3.5 font-bold text-white shadow-lg shadow-indigo-600/25 hover:opacity-90 transition">Daftar Sekarang ✨</button>
        </form>
    </div>
    <p class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Masuk</a></p>
</div>
@endsection
