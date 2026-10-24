@extends('layouts.admin')
@section('title', ($method->exists ? 'Edit' : 'Tambah') . ' Metode Pembayaran')
@section('header', ($method->exists ? 'Edit' : 'Tambah') . ' Pembayaran')
@section('content')
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
            <select name="type" id="input-type" class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm dark:bg-cream-100/5 dark:border-cream-100/15 outline-none">
                <option value="bank_transfer" @selected(old('type', $method->type) === 'bank_transfer')>Transfer Bank</option>
                <option value="qris" @selected(old('type', $method->type) === 'qris')>QRIS (QR link bayar otomatis)</option>
            </select>
        </div>
    </div>
    <div id="bank-fields" class="grid sm:grid-cols-2 gap-4">
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
    <label class="flex items-center gap-2.5 rounded-2xl border border-line p-4 text-sm font-bold dark:border-cream-100/15">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $method->is_active)) class="h-5 w-5 accent-[#be6a42]">
        Tampilkan metode ini saat checkout
    </label>
    <div class="flex items-center gap-3 pt-1">
        <button class="inline-flex items-center gap-2 rounded-2xl bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 px-7 py-3 text-sm font-bold text-white shadow-lg hover:opacity-90 transition"><x-icon name="check" class="h-4 w-4" /> Simpan</button>
        <a href="{{ route('admin.payment-methods.index') }}" class="text-sm text-cocoa-500/70">Batal</a>
    </div>
</form>
<script>
(function () {
    const type = document.getElementById('input-type');
    const bankFields = document.getElementById('bank-fields');
    function refresh() {
        bankFields.style.display = type.value === 'bank_transfer' ? '' : 'none';
    }
    type.addEventListener('change', refresh);
    refresh();
})();
</script>
@endsection
