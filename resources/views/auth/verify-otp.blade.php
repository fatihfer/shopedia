@extends('layouts.app')
@section('title', 'Verifikasi Email - Shopedia')
@section('content')
<div class="mx-auto max-w-md">
    <div class="overflow-hidden rounded-3xl border border-line/70 bg-card dark:bg-night-800 dark:border-cream-100/15 shadow-xl">
        <div class="bg-linear-to-r from-petal-200 to-sand-200 p-7 text-cocoa-900 dark:from-night-900 dark:to-sage-600 dark:text-cream-100">
            <p class="grid h-12 w-12 place-items-center rounded-2xl bg-white/20"><x-icon name="chat" class="h-6 w-6" /></p>
            <h1 class="mt-2 text-2xl font-black tracking-tight">Cek email Anda</h1>
            <p class="text-sm opacity-80">Kode 6 digit dikirim ke <span class="font-bold">{{ session('register_pending.email') }}</span>, berlaku 10 menit.</p>
        </div>
        <form method="POST" action="{{ route('register.verify.store') }}" class="p-7 space-y-4">
            @csrf
            <div>
                <label class="text-sm font-bold">Kode OTP</label>
                <input name="code" required minlength="6" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="••••••"
                    class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-center text-2xl font-black tracking-[0.5em] outline-none focus:border-apricot-400 dark:focus:border-sage-500 dark:bg-cream-100/5 dark:border-cream-100/15 transition">
            </div>
            <button class="w-full rounded-2xl bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 py-3.5 font-bold text-white shadow-lg hover:opacity-90 transition">Verifikasi</button>
        </form>
        <div class="px-7 pb-7 flex items-center justify-between text-sm">
            <form method="POST" action="{{ route('register.resend') }}">
                @csrf
                <button class="font-bold text-cocoa-700 hover:underline dark:text-sage-300">Kirim ulang kode</button>
            </form>
            <a href="{{ route('register') }}" class="text-cocoa-500/70 hover:underline">Ganti email</a>
        </div>
    </div>
</div>
@endsection
