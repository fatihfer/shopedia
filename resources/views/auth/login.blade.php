@extends('layouts.app')
@section('title', 'Masuk - Shopedia')
@section('content')
<div class="mx-auto max-w-md">
    <div class="overflow-hidden rounded-3xl border border-line/70 bg-card dark:bg-night-800 dark:border-cream-100/15 shadow-xl shadow-indigo-500/5">
        <div class="bg-gradient-to-r from-petal-200 to-sand-200 p-7 text-cocoa-900 dark:from-night-900 dark:to-sage-600 dark:text-cream-100">
            <p class="text-3xl">👋</p>
            <h1 class="mt-2 text-2xl font-black tracking-tight">Selamat datang kembali</h1>
            <p class="text-sm text-cocoa-700/80 dark:text-cream-100/70">Masuk untuk checkout & lacak pesananmu.</p>
        </div>
        <form method="POST" action="{{ route('login') }}" class="p-7 space-y-4">
            @csrf
            <div>
                <label class="text-sm font-bold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="kamu@email.com"
                    class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm outline-none focus:border-apricot-400 dark:focus:border-sage-500 focus:ring-4 focus:ring-apricot-400/40 dark:focus:ring-sage-500/20 dark:bg-cream-100/5 dark:border-cream-100/15 transition">
            </div>
            <div>
                <label class="text-sm font-bold">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm outline-none focus:border-apricot-400 dark:focus:border-sage-500 focus:ring-4 focus:ring-apricot-400/40 dark:focus:ring-sage-500/20 dark:bg-cream-100/5 dark:border-cream-100/15 transition">
            </div>
            <label class="flex items-center gap-2 text-sm text-cocoa-500 dark:text-sage-300"><input type="checkbox" name="remember" value="1" class="rounded accent-indigo-600"> Ingat saya</label>
            <button class="w-full rounded-2xl bg-gradient-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 py-3.5 font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50 hover:opacity-90 transition">Masuk →</button>
        </form>
    </div>
    <p class="mt-4 text-center text-sm text-cocoa-500 dark:text-sage-300">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-cocoa-700 dark:text-sage-300 hover:underline">Daftar gratis</a></p>
    <p class="mt-2 text-center text-xs text-cocoa-500/70">Demo admin: admin@shopedia.test / password</p>
</div>
@endsection
