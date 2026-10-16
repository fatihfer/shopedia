<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Shopedia</title>
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
<body class="min-h-screen bg-[#eef2f0] text-gray-900 antialiased dark:bg-night-950 dark:text-cream-100 transition-colors duration-300">

@php
$menu = [
    ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'home', 'label' => 'Dashboard', 'badge' => null],
    ['route' => 'admin.products.index', 'match' => 'admin.products.*', 'icon' => 'box', 'label' => 'Produk', 'badge' => null],
    ['route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'icon' => 'folder', 'label' => 'Kategori', 'badge' => null],
    ['route' => 'admin.orders.index', 'match' => 'admin.orders.*', 'icon' => 'receipt', 'label' => 'Pesanan', 'badge' => $adminPendingCount ?? 0],
    ['route' => 'admin.payment-methods.index', 'match' => 'admin.payment-methods.*', 'icon' => 'card', 'label' => 'Pembayaran', 'badge' => null],
    ['route' => 'admin.subscribers.index', 'match' => 'admin.subscribers.*', 'icon' => 'chat', 'label' => 'Newsletter', 'badge' => null],
];
@endphp

<div class="flex min-h-screen">
    {{-- Sidebar desktop --}}
    <aside class="hidden w-64 shrink-0 flex-col bg-white/80 backdrop-blur border-r border-gray-200/70 lg:flex dark:bg-night-800/80 dark:border-cream-100/10">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-6 pt-7 pb-6">
            <span class="grid h-10 w-10 place-items-center rounded-2xl bg-teal-300 text-teal-950 font-black text-xl dark:bg-sage-500 dark:text-night-950">S</span>
            <span class="leading-tight">
                <span class="block font-extrabold tracking-tight">Shopedia</span>
                <span class="block text-[11px] uppercase tracking-[0.2em] text-gray-400">Admin Panel</span>
            </span>
        </a>
        <nav class="flex-1 space-y-1 px-4">
            @foreach($menu as $item)
            @php $active = request()->routeIs($item['match']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-teal-300 text-teal-950 shadow-md shadow-teal-300/40 dark:bg-sage-500 dark:text-night-950 dark:shadow-black/40' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-sage-300 dark:hover:bg-cream-100/5 dark:hover:text-cream-100' }}">
                <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                <span class="flex-1">{{ $item['label'] }}</span>
                @if(! empty($item['badge']))
                <span class="grid h-5 min-w-5 place-items-center rounded-full bg-red-500 px-1.5 text-[11px] font-bold text-white">{{ $item['badge'] }}</span>
                @endif
                @if($active)
                <x-icon name="arrow-right" class="h-4 w-4" />
                @endif
            </a>
            @endforeach
        </nav>
        <div class="space-y-1 p-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-sage-300 dark:hover:bg-cream-100/5 transition">
                <x-icon name="eye" class="h-5 w-5 shrink-0" /> Lihat Toko
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-500 hover:bg-red-50 hover:text-red-600 dark:text-sage-300 dark:hover:bg-red-500/10 transition">
                    <x-icon name="logout" class="h-5 w-5 shrink-0" /> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Kolom kanan --}}
    <div class="min-w-0 flex-1">
        {{-- Topbar --}}
        <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-gray-200/70 bg-[#eef2f0]/85 px-4 sm:px-8 backdrop-blur-xl dark:bg-night-950/85 dark:border-cream-100/10">
            <button type="button" data-admin-menu class="grid h-10 w-10 place-items-center rounded-xl bg-white border border-gray-200 lg:hidden dark:bg-night-800 dark:border-cream-100/15" aria-label="Menu">
                <x-icon name="menu" class="h-5 w-5" />
            </button>
            <h1 class="flex-1 truncate text-lg sm:text-xl font-extrabold tracking-tight">@yield('header', 'Dashboard')</h1>
            <button type="button" data-theme-toggle data-theme-icon aria-label="Mode gelap"
                class="grid h-10 w-10 place-items-center rounded-full bg-white border border-gray-200 hover:bg-gray-100 dark:bg-night-800 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition">
                <span data-icon-moon><x-icon name="moon" class="h-5 w-5" /></span>
                <span data-icon-sun class="hidden"><x-icon name="sun" class="h-5 w-5" /></span>
            </button>
            <span class="grid h-10 w-10 place-items-center rounded-full bg-teal-300 font-black text-teal-950 dark:bg-sage-500 dark:text-night-950" title="{{ auth()->user()->name }}">
                {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </span>
        </header>

        {{-- Mobile drawer --}}
        <div id="admin-drawer" class="hidden fixed inset-0 z-40 lg:hidden">
            <div class="absolute inset-0 bg-black/40" data-admin-menu></div>
            <aside class="absolute left-0 top-0 flex h-full w-64 flex-col bg-white dark:bg-night-800 shadow-2xl">
                <div class="flex items-center gap-2.5 px-6 pt-6 pb-5">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-teal-300 text-teal-950 font-black text-lg dark:bg-sage-500 dark:text-night-950">S</span>
                    <span class="font-extrabold">Shopedia <span class="block text-[10px] font-medium uppercase tracking-[0.2em] text-gray-400">Admin</span></span>
                </div>
                <nav class="flex-1 space-y-1 px-4 overflow-auto">
                    @foreach($menu as $item)
                    @php $active = request()->routeIs($item['match']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold {{ $active ? 'bg-teal-300 text-teal-950 dark:bg-sage-500 dark:text-night-950' : 'text-gray-500 dark:text-sage-300' }}">
                        <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                        <span class="flex-1">{{ $item['label'] }}</span>
                        @if(! empty($item['badge']))
                        <span class="grid h-5 min-w-5 place-items-center rounded-full bg-red-500 px-1.5 text-[11px] font-bold text-white">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                    @endforeach
                </nav>
                <div class="p-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-500 dark:text-sage-300">
                            <x-icon name="logout" class="h-5 w-5 shrink-0" /> Keluar
                        </button>
                    </form>
                </div>
            </aside>
        </div>

        {{-- Toasts --}}
        @if(session('success') || session('error') || $errors->any())
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-8 pt-5 space-y-2">
            @if(session('success'))
                <div class="toast flex items-start gap-3 rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm text-emerald-800 shadow-sm dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-300">
                    <span class="text-emerald-500"><x-icon name="check-circle" class="h-5 w-5" /></span><span class="flex-1">{{ session('success') }}</span>
                    <button data-toast-close class="opacity-60 hover:opacity-100"><x-icon name="x" class="h-4 w-4" /></button>
                </div>
            @endif
            @if(session('error'))
                <div class="toast flex items-start gap-3 rounded-2xl border border-red-200 bg-white px-4 py-3 text-sm text-red-700 shadow-sm dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-300">
                    <span class="text-amber-500"><x-icon name="alert" class="h-5 w-5" /></span><span class="flex-1">{{ session('error') }}</span>
                    <button data-toast-close class="opacity-60 hover:opacity-100"><x-icon name="x" class="h-4 w-4" /></button>
                </div>
            @endif
            @if($errors->any())
                <div class="toast rounded-2xl border border-red-200 bg-white px-4 py-3 text-sm text-red-700 shadow-sm dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-300">
                    <ul class="list-disc pl-5 space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
        </div>
        @endif

        <main class="mx-auto w-full max-w-6xl px-4 sm:px-8 py-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
document.addEventListener('click', (e) => {
    if (e.target.closest('[data-admin-menu]')) {
        document.getElementById('admin-drawer')?.classList.toggle('hidden');
    }
});
</script>
</body>
</html>
