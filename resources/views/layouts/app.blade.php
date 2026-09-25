<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shopedia')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">
<nav class="border-b bg-white sticky top-0 z-10">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        <a href="{{ route('home') }}" class="text-xl font-bold">Shopedia</a>
        <div class="flex items-center gap-5 text-sm">
            <a href="{{ route('products.index') }}" class="font-medium hover:text-black text-gray-700">Produk</a>
            <a href="{{ route('cart.index') }}" class="hover:text-black text-gray-700">
                Keranjang ({{ array_sum(session('cart', [])) }})
            </a>
            @auth
                <a href="{{ route('orders.index') }}" class="hover:text-black text-gray-700">Pesanan Saya</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="font-medium text-blue-600">Admin</a>
                @endif
                <span class="text-gray-400">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="text-gray-500 hover:text-red-600">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hover:text-black text-gray-700">Login</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-black px-4 py-2 text-white hover:bg-gray-800">Daftar</a>
            @endauth
        </div>
    </div>
</nav>

@if(session('success') || session('error'))
<div class="mx-auto w-full max-w-7xl px-6 pt-4">
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mt-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
</div>
@endif

<main class="mx-auto w-full max-w-7xl flex-1 px-6 py-8">
    @yield('content')
</main>

<footer class="border-t bg-white py-6 text-center text-sm text-gray-400">
    Shopedia &mdash; Ecommerce Laravel sederhana
</footer>
</body>
</html>
