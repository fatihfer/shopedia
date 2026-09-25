@extends('layouts.app')
@section('title', 'Masuk - Shopedia')
@section('content')
<div class="mx-auto max-w-md">
    <div class="overflow-hidden rounded-3xl border border-gray-200/70 bg-white dark:bg-gray-900 dark:border-white/10 shadow-xl shadow-indigo-500/5">
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 p-7 text-white">
            <p class="text-3xl">👋</p>
            <h1 class="mt-2 text-2xl font-black tracking-tight">Selamat datang kembali</h1>
            <p class="text-sm text-white/75">Masuk untuk checkout & lacak pesananmu.</p>
        </div>
        <form method="POST" action="{{ route('login') }}" class="p-7 space-y-4">
            @csrf
            <div>
                <label class="text-sm font-bold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="kamu@email.com"
                    class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 dark:bg-white/5 dark:border-white/10 transition">
            </div>
            <div>
                <label class="text-sm font-bold">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="mt-1.5 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 dark:bg-white/5 dark:border-white/10 transition">
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400"><input type="checkbox" name="remember" value="1" class="rounded accent-indigo-600"> Ingat saya</label>
            <button class="w-full rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 py-3.5 font-bold text-white shadow-lg shadow-indigo-600/25 hover:opacity-90 transition">Masuk →</button>
        </form>
    </div>
    <p class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Daftar gratis</a></p>
    <p class="mt-2 text-center text-xs text-gray-400">Demo admin: admin@shopedia.test / password</p>
</div>
@endsection
