@extends('layouts.app')
@section('title', 'Login - Shopedia')
@section('content')
<div class="mx-auto max-w-md rounded-2xl border bg-white p-8">
    <h1 class="text-2xl font-bold">Login</h1>
    <p class="mt-1 text-sm text-gray-500">Masuk untuk checkout & lihat pesanan.</p>
    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-xl border px-4 py-2.5">
        </div>
        <div>
            <label class="text-sm font-medium">Password</label>
            <input type="password" name="password" required class="mt-1 w-full rounded-xl border px-4 py-2.5">
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-500"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
        <button class="w-full rounded-xl bg-black py-3 font-medium text-white hover:bg-gray-800">Login</button>
    </form>
    <p class="mt-4 text-center text-sm text-gray-500">Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600">Daftar</a></p>
</div>
@endsection
