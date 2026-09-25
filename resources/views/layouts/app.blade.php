<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shopedia — Belanja Mudah & Cepat')</title>
    <meta name="description" content="Shopedia — toko online modern dengan katalog, keranjang, checkout, dan admin panel.">
    <script>
        // Prevent dark-mode flash
        (function () {
            try {
                const saved = localStorage.getItem('shopedia-theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (saved === 'dark' || (!saved && prefersDark)) document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-gray-50 text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100 transition-colors duration-300">

{{-- Announcement bar --}}
<div class="bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 text-white text-center text-xs sm:text-sm py-2 px-4">
    🚚 Gratis ongkir untuk pembelian pertama &nbsp;·&nbsp; Gunakan kode <span class="font-bold tracking-wide">HEMAT10</span>
</div>

{{-- Navbar --}}
<header class="sticky top-0 z-40 border-b border-gray-200/70 bg-white/80 backdrop-blur-xl dark:bg-gray-950/80 dark:border-white/10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="flex h-16 items-center justify-between gap-3">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 text-white font-black text-lg shadow-lg shadow-indigo-600/25">S</span>
                <span class="leading-tight">
                    <span class="block font-extrabold tracking-tight text-lg">Shopedia</span>
                    <span class="hidden sm:block text-[11px] uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500">Modern Store</span>
                </span>
            </a>

            {{-- Search (desktop) --}}
            <form method="GET" action="{{ route('products.index') }}" class="hidden md:flex flex-1 max-w-md items-center">
                <div class="relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">⌕</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari sneakers, kopi, skincare..."
                        class="w-full rounded-full border border-gray-200 bg-gray-100/70 pl-10 pr-4 py-2.5 text-sm outline-none focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 dark:bg-white/5 dark:border-white/10 dark:focus:bg-gray-900 dark:focus:border-indigo-500 transition" />
                </div>
            </form>

            {{-- Actions --}}
            <nav class="flex items-center gap-1.5 sm:gap-2 text-sm">
                <a href="{{ route('products.index') }}" class="hidden sm:inline-flex rounded-full px-3.5 py-2 font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-white/10 dark:hover:text-white transition">Katalog</a>

                {{-- Dark toggle --}}
                <button type="button" data-theme-toggle data-theme-icon aria-label="Mode gelap"
                    class="grid h-10 w-10 place-items-center rounded-full border border-gray-200 text-lg hover:bg-gray-100 dark:border-white/10 dark:hover:bg-white/10 transition">
                    <span data-icon-moon>🌙</span>
                    <span data-icon-sun class="hidden">☀️</span>
                </button>

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}"
                   class="relative grid h-10 w-10 place-items-center rounded-full border border-gray-200 hover:bg-gray-100 dark:border-white/10 dark:hover:bg-white/10 transition" title="Keranjang">
                    🛒
                    @php $cartCount = array_sum(session('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 grid h-5 min-w-5 place-items-center rounded-full bg-gradient-to-r from-indigo-600 to-fuchsia-600 px-1 text-[11px] font-bold text-white shadow">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    <a href="{{ route('orders.index') }}" class="hidden lg:inline-flex rounded-full px-3.5 py-2 font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/10 transition">Pesanan</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex rounded-full bg-gray-900 px-4 py-2 font-semibold text-white hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 transition">Admin ✨</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:inline">
                        @csrf
                        <button class="rounded-full px-3.5 py-2 text-gray-500 hover:text-red-600 dark:text-gray-400 transition" title="Logout ({{ auth()->user()->name }})">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-3.5 py-2 font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/10 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-2 font-semibold text-white shadow-lg shadow-indigo-600/25 hover:opacity-90 transition">Daftar</a>
                @endauth

                {{-- Mobile hamburger --}}
                <button type="button" data-menu-toggle class="sm:hidden grid h-10 w-10 place-items-center rounded-full border border-gray-200 dark:border-white/10" aria-label="Menu">☰</button>
            </nav>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden sm:hidden pb-4 space-y-1">
            <form method="GET" action="{{ route('products.index') }}" class="md:hidden mb-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
                    class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm dark:bg-white/5 dark:border-white/10" />
            </form>
            <a href="{{ route('products.index') }}" class="block rounded-xl px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-white/10 font-medium">Katalog</a>
            <a href="{{ route('cart.index') }}" class="block rounded-xl px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-white/10">Keranjang ({{ array_sum(session('cart', [])) }})</a>
            @auth
                <a href="{{ route('orders.index') }}" class="block rounded-xl px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-white/10">Pesanan Saya</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-4 py-2.5 font-semibold text-indigo-600 dark:text-indigo-400">Admin Dashboard ✨</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full text-left rounded-xl px-4 py-2.5 text-red-500">Keluar ({{ auth()->user()->name }})</button></form>
            @else
                <a href="{{ route('login') }}" class="block rounded-xl px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-white/10">Masuk</a>
            @endauth
        </div>
    </div>
</header>

{{-- Toasts --}}
@if(session('success') || session('error') || $errors->any())
<div class="mx-auto w-full max-w-7xl px-4 sm:px-6 pt-4 space-y-2">
    @if(session('success'))
        <div class="toast flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-300">
            <span class="text-base">✅</span><span class="flex-1">{{ session('success') }}</span>
            <button data-toast-close class="opacity-60 hover:opacity-100">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="toast flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-300">
            <span class="text-base">⚠️</span><span class="flex-1">{{ session('error') }}</span>
            <button data-toast-close class="opacity-60 hover:opacity-100">✕</button>
        </div>
    @endif
    @if($errors->any())
        <div class="toast rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-300">
            <ul class="list-disc pl-5 space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
</div>
@endif

<main class="mx-auto w-full max-w-7xl flex-1 px-4 sm:px-6 py-8">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="mt-8 border-t border-gray-200 bg-white dark:bg-gray-900 dark:border-white/10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 py-12 grid gap-10 md:grid-cols-4">
        <div class="md:col-span-2">
            <div class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 text-white font-black text-lg">S</span>
                <span class="font-extrabold text-lg tracking-tight">Shopedia</span>
            </div>
            <p class="mt-3 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Toko online modern berbasis Laravel — katalog cepat, keranjang instan, checkout aman, dan panel admin yang rapi. Dengan dukungan dark mode otomatis. 🌗
            </p>
            <div class="mt-4 flex gap-2">
                <button data-theme-toggle class="rounded-full border border-gray-200 px-4 py-2 text-xs font-medium hover:bg-gray-100 dark:border-white/10 dark:hover:bg-white/10 transition">🌗 Ganti tema</button>
                <a href="{{ route('products.index') }}" class="rounded-full bg-gray-900 px-4 py-2 text-xs font-semibold text-white hover:bg-gray-700 dark:bg-white dark:text-gray-900 transition">Mulai belanja →</a>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Belanja</h3>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('products.index') }}" class="text-gray-600 hover:text-indigo-600 dark:text-gray-300">Semua produk</a></li>
                <li><a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-indigo-600 dark:text-gray-300">Keranjang</a></li>
                <li><a href="{{ route('checkout.index') }}" class="text-gray-600 hover:text-indigo-600 dark:text-gray-300">Checkout</a></li>
                <li><a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-indigo-600 dark:text-gray-300">Lacak pesanan</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Akun</h3>
            <ul class="mt-3 space-y-2 text-sm">
                @guest
                    <li><a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 dark:text-gray-300">Masuk</a></li>
                    <li><a href="{{ route('register') }}" class="text-gray-600 hover:text-indigo-600 dark:text-gray-300">Daftar</a></li>
                @else
                    <li class="text-gray-500 dark:text-gray-400">Halo, {{ auth()->user()->name }} 👋</li>
                    @if(auth()->user()->isAdmin())
                        <li><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 dark:text-indigo-400 font-medium">Admin dashboard</a></li>
                    @endif
                @endguest
                <li class="text-gray-400 text-xs pt-2">v1.0 · Laravel {{ app()->version() }}</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-gray-100 dark:border-white/5 py-5 text-center text-xs text-gray-400">
        © {{ date('Y') }} Shopedia — dibuat dengan Laravel + Tailwind CSS 💜
    </div>
</footer>
</body>
</html>
