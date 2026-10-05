@extends('layouts.app')
@section('title', 'Metode Pembayaran')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="flex items-center gap-2 text-2xl font-black tracking-tight"><x-icon name="card" class="h-6 w-6 text-ember-600 dark:text-sage-300" /> Metode Pembayaran</h1>
        <p class="text-sm text-cocoa-500 dark:text-sage-300">Rekening & QRIS yang tampil saat checkout.</p>
    </div>
    <a href="{{ route('admin.payment-methods.create') }}" class="rounded-2xl bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg hover:opacity-90 transition">+ Metode</a>
</div>
<div class="mt-5 grid gap-4 sm:grid-cols-2">
    @foreach($methods as $m)
    <div class="rounded-3xl border border-line/70 bg-card p-5 dark:bg-night-800 dark:border-cream-100/15">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-milk-100 dark:bg-cream-100/5"><x-icon name="{{ $m->isQris() ? 'phone' : 'bank' }}" class="h-6 w-6 text-cocoa-500 dark:text-sage-300" /></span>
                <div>
                    <p class="font-extrabold">{{ $m->name }}</p>
                    <p class="text-xs text-cocoa-500/70">
                        @if($m->isQris()) QRIS @else {{ $m->account_number }} a.n. {{ $m->account_name }} @endif
                        · {{ $m->orders_count }} order
                    </p>
                </div>
            </div>
            <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold uppercase {{ $m->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-sand-200/60 text-cocoa-500' }}">{{ $m->is_active ? 'Aktif' : 'Mati' }}</span>
        </div>
        @if($m->qr_image)
        <img src="{{ asset('storage/' . $m->qr_image) }}" alt="QR {{ $m->name }}" class="mt-3 h-28 rounded-2xl border border-line dark:border-cream-100/15">
        @endif
        <div class="mt-3 flex gap-2">
            <a href="{{ route('admin.payment-methods.edit', $m) }}" class="rounded-xl bg-blue-50 px-3.5 py-2 text-xs font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">Edit</a>
            <form method="POST" action="{{ route('admin.payment-methods.destroy', $m) }}" onsubmit="return confirm('Hapus metode ini?')" class="inline">
                @csrf @method('DELETE')
                <button class="rounded-xl bg-red-50 px-3.5 py-2 text-xs font-bold text-red-500 dark:bg-red-500/10">Hapus</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection
