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
<body class="min-h-screen flex flex-col bg-milk-50 text-cocoa-900 antialiased dark:bg-night-950 dark:text-cream-100 transition-colors duration-300">

{{-- Navbar --}}
<header class="sticky top-0 z-40 border-b border-line/70 bg-milk-50/85 backdrop-blur-xl dark:bg-night-950/90 dark:border-cream-100/15">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="flex h-16 items-center justify-between gap-3">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-linear-to-br from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 text-white font-black text-lg shadow-lg shadow-night-900/25 dark:shadow-black/50">S</span>
                <span class="leading-tight">
                    <span class="block font-extrabold tracking-tight text-lg">Shopedia</span>
                    <span class="hidden sm:block text-[11px] uppercase tracking-[0.2em] text-cocoa-500/70 dark:text-sage-400">Modern Store</span>
                </span>
            </a>

            {{-- Search (desktop) --}}
            <form method="GET" action="{{ route('products.index') }}" class="hidden md:flex flex-1 max-w-md items-center">
                <div class="relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-cocoa-500/70"><x-icon name="search" class="h-4 w-4" /></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari sneakers, kopi, skincare..."
                        class="w-full rounded-full border border-line bg-sand-200/50 pl-10 pr-4 py-2.5 text-sm outline-none focus:border-apricot-400 dark:focus:border-sage-500 focus:bg-card focus:ring-4 focus:ring-apricot-400/40 dark:focus:ring-sage-500/20 dark:bg-cream-100/5 dark:border-cream-100/15 dark:focus:bg-night-800 transition" />
                </div>
            </form>

            {{-- Actions --}}
            <nav class="flex items-center gap-1.5 sm:gap-2 text-sm">
                {{-- Dark toggle --}}
                <button type="button" data-theme-toggle data-theme-icon aria-label="Mode gelap"
                    class="grid h-10 w-10 place-items-center rounded-full border border-line text-lg hover:bg-sand-200 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition">
                    <span data-icon-moon><x-icon name="moon" class="h-5 w-5" /></span>
                    <span data-icon-sun class="hidden"><x-icon name="sun" class="h-5 w-5" /></span>
                </button>

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}"
                   class="relative grid h-10 w-10 place-items-center rounded-full border border-line hover:bg-sand-200 dark:border-cream-100/15 dark:hover:bg-cream-100/10 transition" title="Keranjang">
                    <x-icon name="cart" class="h-5 w-5" />
                    @php $cartCount = array_sum(session('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 grid h-5 min-w-5 place-items-center rounded-full bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-1 text-[11px] font-bold text-white shadow">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-gray-900 px-4 py-2 font-semibold text-white hover:bg-gray-700 dark:bg-cream-100 dark:text-night-950 dark:hover:bg-cream-200 transition"><x-icon name="sparkles" class="h-4 w-4" /> Admin</a>
                    @endif
                    {{-- Avatar + dropdown profil --}}
                    <div class="relative hidden sm:block group">
                        <button class="grid h-10 w-10 place-items-center rounded-full bg-night-950 font-black text-cream-100 hover:bg-night-800 dark:bg-cream-100 dark:text-night-950 transition" aria-label="Menu akun">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'P', 0, 1)) }}
                        </button>
                        <div class="absolute right-0 top-full hidden pt-2 group-hover:block group-focus-within:block">
                            <div class="w-56 overflow-hidden rounded-2xl border border-line bg-card shadow-xl dark:bg-night-800 dark:border-cream-100/15">
                                <div class="border-b border-line/60 px-4 py-3 dark:border-cream-100/10">
                                    <p class="truncate text-sm font-extrabold">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-xs text-cocoa-500/70">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="p-2 text-sm font-medium">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 hover:bg-milk-100 dark:hover:bg-cream-100/5 transition"><x-icon name="user" class="h-4 w-4" /> Profil</a>
                                    <a href="{{ route('orders.index') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 hover:bg-milk-100 dark:hover:bg-cream-100/5 transition"><x-icon name="box" class="h-4 w-4" /> Pesanan Saya</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition"><x-icon name="logout" class="h-4 w-4" /> Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-3.5 py-2 font-medium text-cocoa-700 hover:bg-sand-200 dark:text-cream-100/80 dark:hover:bg-cream-100/10 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-4 py-2 font-semibold text-white shadow-lg shadow-night-900/25 dark:shadow-black/50 hover:opacity-90 transition">Daftar</a>
                @endauth

                {{-- Mobile hamburger --}}
                <button type="button" data-menu-toggle class="sm:hidden grid h-10 w-10 place-items-center rounded-full border border-line dark:border-cream-100/15" aria-label="Menu"><x-icon name="menu" class="h-5 w-5" /></button>
            </nav>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden sm:hidden pb-4 space-y-1">
            <form method="GET" action="{{ route('products.index') }}" class="md:hidden mb-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
                    class="w-full rounded-xl border border-line bg-sand-200/60 px-4 py-2.5 text-sm dark:bg-cream-100/5 dark:border-cream-100/15" />
            </form>
            <a href="{{ route('cart.index') }}" class="block rounded-xl px-4 py-2.5 hover:bg-sand-200 dark:hover:bg-cream-100/10">Keranjang ({{ array_sum(session('cart', [])) }})</a>
            @auth
                <a href="{{ route('profile.edit') }}" class="block rounded-xl px-4 py-2.5 hover:bg-sand-200 dark:hover:bg-cream-100/10">Profil ({{ auth()->user()->name }})</a>
                <a href="{{ route('orders.index') }}" class="block rounded-xl px-4 py-2.5 hover:bg-sand-200 dark:hover:bg-cream-100/10">Pesanan Saya</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded-xl px-4 py-2.5 font-semibold text-cocoa-700 dark:text-sage-300"><x-icon name="sparkles" class="h-4 w-4" /> Admin Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full text-left rounded-xl px-4 py-2.5 text-red-500">Keluar ({{ auth()->user()->name }})</button></form>
            @else
                <a href="{{ route('login') }}" class="block rounded-xl px-4 py-2.5 hover:bg-sand-200 dark:hover:bg-cream-100/10">Masuk</a>
            @endauth
        </div>
    </div>
</header>

{{-- Toasts --}}
@if(session('success') || session('error') || $errors->any())
<div class="mx-auto w-full max-w-7xl px-4 sm:px-6 pt-4 space-y-2">
    @if(session('success'))
        <div class="toast flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-300">
            <span class="text-emerald-500"><x-icon name="check-circle" class="h-5 w-5" /></span><span class="flex-1">{{ session('success') }}</span>
            <button data-toast-close class="opacity-60 hover:opacity-100"><x-icon name="x" class="h-4 w-4" /></button>
        </div>
    @endif
    @if(session('error'))
        <div class="toast flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-300">
            <span class="text-amber-500"><x-icon name="alert" class="h-5 w-5" /></span><span class="flex-1">{{ session('error') }}</span>
            <button data-toast-close class="opacity-60 hover:opacity-100"><x-icon name="x" class="h-4 w-4" /></button>
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

{{-- Floating WhatsApp --}}
@if(config('contact.admin_wa'))
<a href="https://wa.me/{{ config('contact.admin_wa') }}?text={{ urlencode(config('contact.greeting')) }}" target="_blank" title="Chat admin via WhatsApp"
   class="fixed bottom-5 right-5 z-40 grid h-14 w-14 place-items-center rounded-full bg-[#25D366] text-white shadow-xl shadow-emerald-600/30 transition hover:scale-105">
    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2c-1.5 0-3-.4-4.3-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.6-6.1c-.3-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.3-.6.8-.8 1-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4 0-.5.1-.7l.5-.6c.1-.2.1-.4 0-.5L9.4 8.2c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.5.2-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.6-.3z"/></svg>
</a>
@endif

{{-- Footer --}}
<footer class="mt-8 border-t border-line bg-card dark:bg-night-800 dark:border-cream-100/15">
    {{-- Trust strip --}}
    <div class="border-b border-line/60 dark:border-cream-100/10">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-4 px-4 sm:px-6 py-6 lg:grid-cols-4">
            @php
            $trusts = [
                ['shield', '100% Original', 'Garansi uang kembali'],
                ['truck', 'Pengiriman Cepat', 'JNE, J&T, SiCepat, GoSend'],
                ['card', 'Pembayaran Lengkap', 'QRIS & transfer bank'],
                ['chat', 'CS Fast Respon', 'Senin–Sabtu via WhatsApp'],
            ];
            @endphp
            @foreach($trusts as [$icon, $title, $desc])
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-milk-100 dark:bg-cream-100/5"><x-icon :name="$icon" class="h-5 w-5 text-ember-600 dark:text-sage-300" /></span>
                <span><span class="block text-xs font-extrabold">{{ $title }}</span><span class="block text-[11px] text-cocoa-500/70">{{ $desc }}</span></span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 py-12 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-linear-to-br from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 text-white font-black text-lg">S</span>
                <span class="font-extrabold text-lg tracking-tight">Shopedia</span>
            </div>
            <p class="mt-3 max-w-xs text-sm leading-6 text-cocoa-500 dark:text-sage-300">
                Sneakers, skincare, kopi sampai elektronik — 100% original, dikirim cepat ke seluruh Indonesia.
            </p>
            <div class="mt-4 flex gap-2">
                @php
                $socials = [
                    ['instagram', 'Instagram', 'https://instagram.com'],
                    ['facebook', 'Facebook', 'https://facebook.com'],
                    ['x-logo', 'X', 'https://x.com'],
                    ['youtube', 'YouTube', 'https://youtube.com'],
                ];
                @endphp
                @foreach($socials as [$icon, $label, $url])
                <a href="{{ $url }}" target="_blank" title="{{ $label }}" class="grid h-9 w-9 place-items-center rounded-full border border-line text-cocoa-500 hover:bg-night-950 hover:text-cream-100 hover:border-night-950 dark:border-cream-100/15 dark:text-sage-300 dark:hover:bg-cream-100 dark:hover:text-night-950 transition"><x-icon :name="$icon" class="h-4 w-4" /></a>
                @endforeach
                <button data-theme-toggle title="Ganti tema" class="grid h-9 w-9 place-items-center rounded-full border border-line text-cocoa-500 hover:bg-sand-200 dark:border-cream-100/15 dark:text-sage-300 dark:hover:bg-cream-100/10 transition"><x-icon name="theme" class="h-4 w-4" /></button>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-cocoa-500/70">Belanja</h3>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('products.index') }}" class="text-cocoa-700 hover:underline dark:text-cream-100/80">Semua produk</a></li>
                @foreach($footerCategories as $cat)
                <li><a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="text-cocoa-700 hover:underline dark:text-cream-100/80">{{ $cat->name }}</a></li>
                @endforeach
                <li><a href="{{ route('cart.index') }}" class="text-cocoa-700 hover:underline dark:text-cream-100/80">Keranjang</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-cocoa-500/70">Bantuan</h3>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('orders.index') }}" class="text-cocoa-700 hover:underline dark:text-cream-100/80">Lacak pesanan</a></li>
                <li><a href="{{ route('contact') }}" class="text-cocoa-700 hover:underline dark:text-cream-100/80">Hubungi Kami</a></li>
                <li><a href="{{ route('info.show', 'syarat-ketentuan') }}" class="text-cocoa-700 hover:underline dark:text-cream-100/80">Syarat & Ketentuan</a></li>
                <li><a href="{{ route('info.show', 'kebijakan-privasi') }}" class="text-cocoa-700 hover:underline dark:text-cream-100/80">Kebijakan Privasi</a></li>
                <li><a href="{{ route('info.show', 'pengembalian-barang') }}" class="text-cocoa-700 hover:underline dark:text-cream-100/80">Pengembalian Barang</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-cocoa-500/70">Dapatkan Promo</h3>
            <p class="mt-3 text-sm text-cocoa-500 dark:text-sage-300">Daftarkan email untuk info diskon & produk baru.</p>
            <form method="POST" action="{{ route('newsletter.store') }}" class="mt-3 flex gap-2">
                @csrf
                <input type="email" name="email" required maxlength="100" placeholder="kamu@email.com"
                    class="min-w-0 flex-1 rounded-2xl border border-line bg-milk-100 px-4 py-2.5 text-sm outline-none focus:border-apricot-400 dark:focus:border-sage-500 dark:bg-cream-100/5 dark:border-cream-100/15 transition">
                <button class="shrink-0 rounded-2xl bg-night-950 px-4 py-2.5 text-sm font-bold text-cream-100 hover:bg-night-800 dark:bg-cream-100 dark:text-night-950 dark:hover:bg-cream-200 transition">OK</button>
            </form>
            <div class="mt-4 space-y-1.5 text-sm text-cocoa-500 dark:text-sage-300">
                <p>{{ config('contact.hours') }}</p>
                @if(config('contact.admin_wa'))
                <p>WA: {{ config('contact.admin_wa') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Pembayaran & ekspedisi --}}
    <div class="border-t border-line/60 dark:border-cream-100/10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 py-5 space-y-3 text-xs">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-bold uppercase tracking-wider text-cocoa-500/70">Bayar via:</span>
                @foreach($footerPayments as $pay)
                <span class="rounded-lg border border-line bg-milk-100 px-2.5 py-1 font-bold dark:border-cream-100/15 dark:bg-cream-100/5">{{ $pay->name }}</span>
                @endforeach
                <span class="rounded-lg border border-line bg-milk-100 px-2.5 py-1 font-bold dark:border-cream-100/15 dark:bg-cream-100/5">COD</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-bold uppercase tracking-wider text-cocoa-500/70">Ekspedisi:</span>
                @foreach(config('shipping.couriers') as $courier)
                <span class="rounded-lg border border-line bg-milk-100 px-2.5 py-1 font-bold dark:border-cream-100/15 dark:bg-cream-100/5">{{ $courier }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <div class="border-t border-line/60 dark:border-cream-100/10 py-5 text-center text-xs text-cocoa-500/70">
        © {{ date('Y') }} Shopedia · 100% Original · Harga dalam Rupiah (IDR)
    </div>
</footer>
</body>
</html>
