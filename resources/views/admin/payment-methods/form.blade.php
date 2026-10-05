@extends('layouts.app')
@section('title', ($method->exists ? 'Edit' : 'Tambah') . ' Metode Pembayaran')
@section('content')
<h1 class="text-center text-2xl font-black tracking-tight">{{ $method->exists ? '✏️ Edit' : '➕ Tambah' }} Metode Pembayaran</h1>
<form method="POST" action="{{ $method->exists ? route('admin.payment-methods.update', $method) : route('admin.payment-methods.store') }}" enctype="multipart/form-data"
    class="mx-auto max-w-2xl space-y-4 rounded-3xl border border-line/70 bg-card p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
    @csrf
    @if($method->exists) @method('PUT') @endif
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-bold">Nama <span class="font-normal text-cocoa-500/70">(misal: QRIS, BRI)</span></label>
            <input name="name" value="{{ old('name', $method->name) }}" required
                class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none focus:border-apricot-400 dark:focus:border-sage-500">
        </div>
        <div>
            <label class="text-sm font-bold">Tipe</label>
            <select name="type" class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none">
                <option value="bank_transfer" @selected(old('type', $method->type) === 'bank_transfer')>🏦 Transfer Bank</option>
                <option value="qris" @selected(old('type', $method->type) === 'qris')>📱 QRIS</option>
            </select>
        </div>
    </div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-bold">Nomor rekening <span class="font-normal text-cocoa-500/70">(wajib untuk bank)</span></label>
            <input name="account_number" value="{{ old('account_number', $method->account_number) }}" placeholder="cth: 1234567890"
                class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none">
        </div>
        <div>
            <label class="text-sm font-bold">Atas nama</label>
            <input name="account_name" value="{{ old('account_name', $method->account_name) }}" placeholder="cth: Shopedia"
                class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none">
        </div>
    </div>
    <div>
        <label class="text-sm font-bold">Gambar QR <span class="font-normal text-cocoa-500/70">(untuk QRIS — PNG hasil unduh dari aplikasi bank/e-wallet)</span></label>
        @if($method->qr_image)
            <img src="{{ asset('storage/' . $method->qr_image) }}" alt="QR saat ini" class="mt-2 h-32 rounded-2xl border border-line dark:border-cream-100/15">
            <p class="mt-1 text-xs text-cocoa-500/70">Upload baru untuk mengganti.</p>
        @endif
        <input type="file" name="qr_image" accept="image/*"
            class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-2.5 text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-night-950 file:px-4 file:py-2 file:text-xs file:font-bold file:text-cream-100 dark:bg-cream-100/5 dark:border-cream-100/15">
    </div>
    <label class="flex items-center gap-2.5 rounded-2xl border border-line p-4 text-sm font-bold dark:border-cream-100/15">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $method->is_active)) class="h-5 w-5 accent-[#be6a42]">
        Tampilkan metode ini saat checkout
    </label>
    <div class="flex items-center gap-3 pt-1">
        <button class="rounded-2xl bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-7 py-3 text-sm font-bold text-white shadow-lg hover:opacity-90 transition">💾 Simpan</button>
        <a href="{{ route('admin.payment-methods.index') }}" class="text-sm text-cocoa-500/70">Batal</a>
    </div>
</form>
@endsection
