@extends('layouts.app')
@section('title', 'Hubungi Kami - Shopedia')
@section('content')
<div class="mx-auto max-w-2xl">
    <div class="overflow-hidden rounded-3xl border border-line/70 bg-card dark:bg-night-800 dark:border-cream-100/15">
        <div class="bg-linear-to-r from-petal-200 to-sand-200 p-7 text-cocoa-900 dark:from-night-900 dark:to-sage-600 dark:text-cream-100">
            <p class="grid h-12 w-12 place-items-center rounded-2xl bg-white/20"><x-icon name="chat" class="h-6 w-6" /></p>
            <h1 class="mt-2 text-2xl font-black tracking-tight">Hubungi Kami</h1>
            <p class="text-sm opacity-80">Ada pertanyaan soal produk, pembayaran, atau pesanan? Chat admin langsung.</p>
        </div>
        <div class="p-7 space-y-4">
            <div class="flex items-center gap-3 rounded-2xl bg-milk-100 p-4 text-sm dark:bg-cream-100/5">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-milk-100 dark:bg-cream-100/5"><x-icon name="clock" class="h-5 w-5 text-cocoa-500 dark:text-sage-300" /></span>
                <div><p class="font-bold">Jam operasional</p><p class="text-cocoa-500/70">{{ config('contact.hours') }}</p></div>
            </div>
            @if(config('contact.admin_wa'))
            <a href="https://wa.me/{{ config('contact.admin_wa') }}?text={{ urlencode(config('contact.greeting')) }}" target="_blank"
               class="flex items-center justify-center gap-2.5 rounded-2xl bg-[#25D366] py-4 font-bold text-white shadow-lg hover:opacity-90 transition">
                Chat Admin via WhatsApp
            </a>
            @else
            <p class="rounded-2xl bg-sand-200/60 p-4 text-sm text-cocoa-500">Nomor WhatsApp admin belum dikonfigurasi.</p>
            @endif

            @auth
                @php $myOrders = auth()->user()->orders()->latest()->take(3)->get(); @endphp
                @if($myOrders->isNotEmpty())
                <div>
                    <h2 class="text-sm font-extrabold uppercase tracking-wider text-cocoa-500/70">Tanya tentang pesananmu</h2>
                    <div class="mt-2 space-y-2">
                        @foreach($myOrders as $o)
                        <a href="https://wa.me/{{ config('contact.admin_wa') }}?text={{ urlencode('Halo Shopedia, saya mau bertanya tentang pesanan #' . $o->id . ' (Rp' . number_format($o->total_price, 0, ',', '.') . ')') }}" target="_blank"
                           class="flex items-center justify-between rounded-2xl border border-line px-4 py-3 text-sm hover:bg-milk-100 dark:border-cream-100/15 dark:hover:bg-cream-100/5 transition">
                            <span class="font-bold">Pesanan #{{ $o->id }} · {{ ucfirst(str_replace('_', ' ', $o->status)) }}</span>
                            <span class="font-bold text-emerald-600">Tanya <x-icon name="arrow-right" class="inline h-4 w-4" /></span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection
