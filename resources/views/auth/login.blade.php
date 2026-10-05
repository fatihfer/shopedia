@extends('layouts.app')
@section('title', 'Masuk - Shopedia')
@section('content')
<div class="mx-auto max-w-md">
    <div class="overflow-hidden rounded-3xl border border-line/70 bg-card dark:bg-night-800 dark:border-cream-100/15 shadow-xl shadow-indigo-500/5">
        <div class="bg-gradient-to-r from-petal-200 to-sand-200 p-7 text-cocoa-900 dark:from-night-900 dark:to-sage-600 dark:text-cream-100">
            <p class="grid h-12 w-12 place-items-center rounded-2xl bg-white/20"><x-icon name="login" class="h-6 w-6" /></p>
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
            <button class="flex w-full items-center justify-center gap-2 rounded-2xl bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 py-3.5 font-bold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50 hover:opacity-90 transition">Masuk <x-icon name="arrow-right" class="h-4 w-4" /></button>
        </form>
        <div class="px-7 pb-7">
            <div class="flex items-center gap-3 text-xs text-cocoa-500/70 dark:text-sage-300">
                <span class="h-px flex-1 bg-line dark:bg-cream-100/15"></span> atau <span class="h-px flex-1 bg-line dark:bg-cream-100/15"></span>
            </div>
            <a href="{{ route('google.redirect') }}" class="mt-4 flex w-full items-center justify-center gap-2.5 rounded-2xl border border-line bg-card px-4 py-3.5 text-sm font-bold hover:bg-milk-100 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition">
                <svg class="h-5 w-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.5 12.3c0-.9-.1-1.5-.3-2.3H12v4.5h6.5c-.1 1.1-.8 2.7-2.4 3.8l-.1.1 3.5 2.7.2.1c2.2-2 3.6-5 3.6-8.9z"/><path fill="#34A853" d="M12 24c3.2 0 5.9-1.1 7.9-2.9l-3.8-2.9c-1 .7-2.4 1.2-4.1 1.2-3.1 0-5.8-2.1-6.8-5l-.1.1-3.6 2.8v.1C3.5 21.4 7.5 24 12 24z"/><path fill="#FBBC05" d="M5.2 14.4c-.2-.7-.4-1.5-.4-2.4s.1-1.7.4-2.4l-.1-.1-3.5-2.7-.1.1C.6 8.6 0 10.2 0 12s.6 3.4 1.5 4.9l3.7-2.5z"/><path fill="#EA4335" d="M12 4.7c1.8 0 3 .8 3.7 1.4l3.3-3.2C17.9 1.1 15.2 0 12 0 7.5 0 3.5 2.6 1.5 6.9l3.7 2.9c1-2.9 3.7-5.1 6.8-5.1z"/></svg>
                Lanjut dengan Google
            </a>
        </div>
    </div>
    <p class="mt-4 text-center text-sm text-cocoa-500 dark:text-sage-300">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-cocoa-700 dark:text-sage-300 hover:underline">Daftar gratis</a></p>
    {{-- <p class="mt-2 text-center text-xs text-cocoa-500/70">Demo admin: admin@shopedia.test / password</p> --}}
</div>
@endsection
