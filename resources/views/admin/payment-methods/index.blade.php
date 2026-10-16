@extends('layouts.admin')
@section('title', 'Metode Pembayaran')
@section('header', 'Pembayaran')
@section('content')
<div class="mb-4 flex items-center justify-between gap-3">
    <p class="text-sm text-gray-500 dark:text-sage-300">Rekening & QRIS yang tampil saat checkout.</p>
    <a href="{{ route('admin.payment-methods.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-night-950 px-4 py-2.5 text-sm font-bold text-cream-100 hover:bg-night-800 dark:bg-cream-100 dark:text-night-950 transition"><x-icon name="plus" class="h-4 w-4" /> Metode</a>
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
                        @if($m->isGateway()) <span class="inline-flex items-center gap-1"><x-icon name="bolt" class="h-3.5 w-3.5" /> QR dinamis Midtrans</span>
                        @elseif($m->isQris()) QRIS manual
                        @else {{ $m->account_number }} a.n. {{ $m->account_name }} @endif
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
