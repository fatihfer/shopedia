@extends('layouts.app')
@section('title', 'Pesanan #' . $order->id)
@section('content')
@php
$badge = [
    'awaiting_payment' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
    'awaiting_verification' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300',
    'processing' => 'bg-violet-100 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300',
    'shipped' => 'bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300',
    'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
    'cancelled' => 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-300',
    'expired' => 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-300',
];
$label = [
    'awaiting_payment' => 'Menunggu pembayaran',
    'awaiting_verification' => 'Menunggu verifikasi',
    'processing' => 'Diproses',
    'shipped' => 'Dikirim',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan',
    'expired' => 'Kadaluarsa',
];
@endphp
<a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-cocoa-500 hover:text-cocoa-700 dark:text-sage-300 transition"><x-icon name="arrow-left" class="h-4 w-4" /> Semua pesanan</a>

@if($order->rejection_reason)
<div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-300">
    <span class="inline-flex items-center gap-1.5"><x-icon name="x" class="h-4 w-4" /> Bukti pembayaran ditolak admin:</span> <span class="font-semibold">{{ $order->rejection_reason }}</span> Silakan upload ulang bukti yang valid.
</div>
@endif

@if($order->status === 'expired')
<div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-300">
    <span class="inline-flex items-center gap-1.5"><x-icon name="clock" class="h-4 w-4" /> Batas pembayaran 24 jam terlewati, pesanan otomatis dibatalkan dan stok dikembalikan.</span> Silakan buat pesanan baru.
</div>
@endif

<div class="mt-4 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 rounded-3xl border border-line/70 bg-card p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-black">Pesanan #{{ $order->id }}</h1>
            <span class="rounded-full px-3.5 py-1.5 text-xs font-bold uppercase {{ $badge[$order->status] ?? '' }}">{{ $label[$order->status] ?? $order->status }}</span>
        </div>
        <p class="mt-1 text-sm text-cocoa-500 dark:text-sage-300">
            {{ $order->created_at->format('d M Y H:i') }} · {{ $order->paymentMethod->name ?? 'Transfer manual' }}
        </p>
        {{-- Progress --}}
        @php $steps = ['awaiting_payment','awaiting_verification','processing','shipped','completed']; $idx = array_search($order->status, $steps); @endphp
        @if(! in_array($order->status, ['cancelled', 'expired'], true))
        <div class="mt-5 flex items-center gap-1.5">
            @foreach($steps as $i => $s)
                <div class="flex-1 h-2 rounded-full {{ $idx !== false && $i <= $idx ? 'bg-linear-to-r from-sage-400 to-sage-600' : 'bg-sand-200 dark:bg-cream-100/10' }}"></div>
            @endforeach
        </div>
        <p class="mt-2 text-xs text-cocoa-500/70">Bayar → Verifikasi → Diproses → Dikirim → Selesai</p>
        @endif
        <div class="mt-5 space-y-3">
            @foreach($order->items as $item)
            <div class="flex justify-between gap-3 border-b border-line/60 pb-3 text-sm dark:border-cream-100/10">
                <div><p class="font-bold">{{ $item->product->name ?? 'Produk dihapus' }}</p><p class="text-cocoa-500">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p></div>
                <p class="font-extrabold whitespace-nowrap">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
        <div class="mt-4 flex justify-between font-black text-lg"><span>Total</span><span class="text-gradient">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>
    </div>

    <div class="space-y-6">
        @if($order->isUnpaid())
        <div class="rounded-3xl border border-line/70 bg-card p-6 dark:bg-night-800 dark:border-cream-100/15">
            <h2 class="flex items-center gap-2 font-extrabold"><x-icon name="card" class="h-5 w-5 text-ember-600 dark:text-sage-300" /> Bayar Pesanan</h2>
            @if($order->paymentMethod)
                @if($order->paymentMethod->isQris())
                    <p class="mt-2 text-sm text-cocoa-500">Scan QR {{ $order->paymentMethod->name }} sejumlah nominal persis:</p>
                    @if($order->paymentMethod->qr_image)
                        <img src="{{ asset('storage/' . $order->paymentMethod->qr_image) }}" alt="QRIS {{ $order->paymentMethod->name }}" class="mx-auto mt-3 w-52 rounded-2xl border border-line dark:border-cream-100/15">
                    @else
                        <p class="mt-3 rounded-2xl bg-sand-200/60 p-3 text-center text-xs text-cocoa-500">Gambar QR segera tersedia.</p>
                    @endif
                @else
                    <p class="mt-2 text-sm text-cocoa-500">Transfer ke rekening berikut sejumlah nominal persis:</p>
                    <div class="mt-3 rounded-2xl bg-milk-100 p-4 dark:bg-cream-100/5">
                        <p class="text-xs text-cocoa-500/70">{{ $order->paymentMethod->name }}</p>
                        <p class="text-xl font-black tracking-wider">{{ $order->paymentMethod->account_number }}</p>
                        <p class="text-xs text-cocoa-500/70">a.n. {{ $order->paymentMethod->account_name }}</p>
                    </div>
                @endif
            @endif
            <div class="mt-3 flex justify-between rounded-2xl bg-sand-200/60 p-3 text-sm dark:bg-sage-500/10">
                <span class="font-medium">Nominal</span>
                <span class="font-black">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <p class="mt-2 flex items-center justify-center gap-1.5 text-center text-xs {{ $order->expired_at?->isPast() ? 'text-red-500 font-bold' : 'text-cocoa-500/70' }}">
                <x-icon name="clock" class="h-4 w-4" /> Bayar sebelum {{ $order->expired_at?->format('d M Y H:i') }}
            </p>

            @if($order->payment_proof)
            <div class="mt-3 rounded-2xl border border-blue-200 bg-blue-50 p-3 dark:bg-blue-500/10 dark:border-blue-500/20">
                <p class="flex items-center gap-1.5 text-xs font-bold text-blue-700 dark:text-blue-300"><x-icon name="send" class="h-4 w-4" /> Bukti sudah terkirim — menunggu verifikasi admin</p>
                <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank">
                    <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti terkirim" class="mt-2 h-28 rounded-xl border border-line dark:border-cream-100/15 hover:opacity-90 transition">
                </a>
                <p class="mt-1 text-[11px] text-cocoa-500/70">Klik gambar untuk perbesar. Upload ulang di bawah untuk mengganti (bukti lama terhapus).</p>
            </div>
            @endif

            <form method="POST" action="{{ route('orders.proof', $order) }}" enctype="multipart/form-data" class="mt-4">
                @csrf
                <label class="text-sm font-bold">{{ $order->status === 'awaiting_verification' ? 'Ganti bukti pembayaran' : 'Upload bukti pembayaran' }}</label>
                <input type="file" name="proof" accept="image/*" required
                    class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-2.5 text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-night-950 file:px-4 file:py-2 file:text-xs file:font-bold file:text-cream-100 dark:bg-cream-100/5 dark:border-cream-100/15">
                <p class="mt-1 text-xs text-cocoa-500/70">JPG/PNG/WebP, maks 2MB.</p>
                <button class="mt-3 flex w-full items-center justify-center gap-2 rounded-2xl bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 py-3 text-sm font-bold text-white hover:opacity-90 transition"><x-icon name="send" class="h-4 w-4" /> Kirim Bukti</button>
            </form>

            <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Batalkan pesanan ini?')" class="mt-2">
                @csrf
                <button class="w-full py-2 text-center text-xs text-cocoa-500/70 hover:text-red-500 transition">Batalkan pesanan</button>
            </form>
        </div>
        @elseif($order->paid_at)
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-6 text-sm text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-300">
            <p class="flex items-center gap-1.5"><x-icon name="check-circle" class="h-5 w-5" /> Lunas via {{ $order->paymentMethod->name ?? '-' }} pada {{ $order->paid_at->format('d M Y H:i') }}.</p>
        </div>
        @endif

        <div class="h-fit rounded-3xl border border-line/70 bg-card p-6 dark:bg-night-800 dark:border-cream-100/15">
            <h2 class="flex items-center gap-2 font-extrabold"><x-icon name="pin" class="h-5 w-5 text-ember-600 dark:text-sage-300" /> Pengiriman</h2>
            <p class="mt-2 text-sm font-bold">{{ $order->recipient_name }} <span class="font-normal text-cocoa-500/70">({{ $order->recipient_phone }})</span></p>
            <p class="mt-1 text-sm leading-6 text-cocoa-700 dark:text-cream-100/80">{{ $order->shipping_address }}</p>
            @if($order->latitude && $order->longitude)
            <a href="https://www.openstreetmap.org/?mlat={{ $order->latitude }}&mlon={{ $order->longitude }}#map=16/{{ $order->latitude }}/{{ $order->longitude }}" target="_blank" class="mt-2 inline-block text-xs font-bold text-cocoa-700 underline dark:text-sage-300">📌 Lihat pin lokasi di peta</a>
            @endif
            @if($order->courier)
            <div class="mt-3 space-y-1 border-t border-dashed border-line pt-3 text-sm dark:border-cream-100/15">
                <div class="flex justify-between"><span class="text-cocoa-500/70">Kurir</span><span class="font-bold">{{ config('shipping.couriers.' . $order->courier, $order->courier) }} · {{ config('shipping.services.' . $order->shipping_service . '.label', $order->shipping_service) }}</span></div>
                <div class="flex justify-between"><span class="text-cocoa-500/70">Ongkir</span><span class="font-bold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
