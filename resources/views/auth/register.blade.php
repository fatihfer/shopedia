@extends('layouts.app')
@section('title', 'Daftar - Shopedia')
@section('content')
<div class="mx-auto max-w-md rounded-2xl border bg-white p-8">
    <h1 class="text-2xl font-bold">Buat Akun</h1>
    <p class="mt-1 text-sm text-gray-500">Daftar sebagai customer.</p>
    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-xl border px-4 py-2.5">
        </div>
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-xl border px-4 py-2.5">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">Password</label>
                <input type="password" name="password" required class="mt-1 w-full rounded-xl border px-4 py-2.5">
            </div>
            <div>
                <label class="text-sm font-medium">Konfirmasi</label>
                <input type="password" name="password_confirmation" required class="mt-1 w-full rounded-xl border px-4 py-2.5">
            </div>
        </div>
        <button class="w-full rounded-xl bg-black py-3 font-medium text-white hover:bg-gray-800">Daftar</button>
    </form>
    <p class="mt-4 text-center text-sm text-gray-500">Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600">Login</a></p>
</div>
@endsection
