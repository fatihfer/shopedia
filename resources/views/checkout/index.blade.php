@extends('layouts.app')
@section('title', 'Checkout - Shopedia')
@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<div class="mx-auto max-w-5xl">
    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Checkout 💳</h1>
    <p class="text-sm text-cocoa-500 dark:text-sage-300">Lengkapi data penerima & pengiriman.</p>

    {{-- Steps --}}
    <div class="mt-5 flex items-center gap-2 text-xs font-semibold">
        <span class="rounded-full bg-emerald-500 px-3.5 py-1.5 text-white">1 Keranjang ✓</span>
        <span class="text-sand-300">─</span>
        <span class="rounded-full bg-night-950 px-3.5 py-1.5 text-cream-100 dark:bg-sage-500 dark:text-night-950">2 Data & Bayar</span>
        <span class="text-sand-300">─</span>
        <span class="rounded-full bg-sand-200 px-3.5 py-1.5 text-cocoa-500 dark:bg-cream-100/10 dark:text-sage-300">3 Selesai</span>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form" class="lg:col-span-2 space-y-6">
            @csrf
            {{-- Penerima --}}
            <div class="rounded-3xl border border-line/70 bg-card p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
                <h2 class="font-extrabold flex items-center gap-2">👤 Data Penerima</h2>
                <div class="mt-3 grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-bold">Nama penerima</label>
                        <input name="recipient_name" value="{{ old('recipient_name', auth()->user()->name) }}" required maxlength="100" placeholder="cth: Budi Santoso"
                            class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm outline-none focus:border-apricot-400 dark:focus:border-sage-500 dark:bg-cream-100/5 dark:border-cream-100/15 transition">
                    </div>
                    <div>
                        <label class="text-sm font-bold">No. HP / WhatsApp</label>
                        <input name="recipient_phone" value="{{ old('recipient_phone') }}" required maxlength="20" placeholder="cth: 08123456789"
                            class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm outline-none focus:border-apricot-400 dark:focus:border-sage-500 dark:bg-cream-100/5 dark:border-cream-100/15 transition">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="text-sm font-bold">Alamat lengkap</label>
                    <textarea name="shipping_address" required rows="3" placeholder="Jalan, nomor rumah, patokan, kota, kode pos..."
                        class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm leading-6 outline-none focus:border-apricot-400 dark:focus:border-sage-500 focus:ring-4 focus:ring-apricot-400/40 dark:focus:ring-sage-500/20 dark:bg-cream-100/5 dark:border-cream-100/15 transition">{{ old('shipping_address') }}</textarea>
                </div>
                <div class="mt-3">
                    <label class="text-sm font-bold">📍 Pin lokasi <span class="font-normal text-cocoa-500/70">(opsional — klik peta untuk menandai)</span></label>
                    <div id="pickup-map" class="mt-1.5 h-56 w-full overflow-hidden rounded-2xl border border-line dark:border-cream-100/15 z-0"></div>
                    <p id="pickup-label" class="mt-1.5 text-xs text-cocoa-500/70">Belum ada titik dipilih.</p>
                    <input type="hidden" name="latitude" id="input-lat" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="input-lng" value="{{ old('longitude') }}">
                </div>
            </div>

            {{-- Kurir --}}
            <div class="rounded-3xl border border-line/70 bg-card p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
                <h2 class="font-extrabold flex items-center gap-2">🚚 Kurir & Layanan</h2>
                <label class="mt-3 block text-sm font-bold">Kurir</label>
                <select name="courier" id="input-courier" required
                    class="mt-1.5 w-full rounded-2xl border border-line bg-milk-100 px-4 py-3 text-sm outline-none focus:border-apricot-400 dark:focus:border-sage-500 dark:bg-cream-100/5 dark:border-cream-100/15 transition">
                    @foreach($couriers as $code => $name)
                        <option value="{{ $code }}" @selected(old('courier', 'jne') === $code)>{{ $name }}</option>
                    @endforeach
                </select>
                <label class="mt-4 block text-sm font-bold">Layanan <span class="font-normal text-cocoa-500/70">(ongkir flat)</span></label>
                <div class="mt-1.5 space-y-2" id="service-list">
                    @foreach($services as $code => $service)
                    <label class="service-option flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-line p-4 transition has-checked:border-apricot-400 has-checked:bg-sand-200/50 dark:border-cream-100/15 dark:has-checked:border-sage-500 dark:has-checked:bg-sage-500/10"
                        data-couriers="{{ implode(',', $service['couriers']) }}">
                        <span class="flex items-center gap-3">
                            <input type="radio" name="shipping_service" value="{{ $code }}" data-cost="{{ $service['cost'] }}" @checked(old('shipping_service', 'reguler') === $code) class="h-5 w-5 accent-[#be6a42]" required>
                            <span class="font-bold text-sm">{{ $service['label'] }}</span>
                        </span>
                        <span class="text-sm font-extrabold whitespace-nowrap">Rp {{ number_format($service['cost'], 0, ',', '.') }}</span>
                    </label>
                    @endforeach
                </div>
                <p id="service-warning" class="mt-2 hidden text-xs font-semibold text-red-500">Layanan ini tidak tersedia untuk kurir tersebut — pilih layanan lain.</p>
            </div>

            {{-- Bayar --}}
            <div class="rounded-3xl border border-line/70 bg-card p-6 sm:p-8 dark:bg-night-800 dark:border-cream-100/15">
                <h2 class="font-extrabold flex items-center gap-2">💳 Metode Pembayaran</h2>
                @if($methods->isEmpty())
                    <p class="mt-3 rounded-2xl bg-red-50 p-4 text-sm text-red-600 dark:bg-red-500/10 dark:text-red-300">Belum ada metode pembayaran aktif. Hubungi admin toko.</p>
                @else
                <div class="mt-3 space-y-2.5">
                    @foreach($methods as $method)
                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-line p-4 transition has-checked:border-apricot-400 has-checked:bg-sand-200/50 dark:border-cream-100/15 dark:has-checked:border-sage-500 dark:has-checked:bg-sage-500/10">
                        <input type="radio" name="payment_method_id" value="{{ $method->id }}" @checked($loop->first) class="h-5 w-5 shrink-0 accent-[#be6a42]" required>
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-milk-100 text-xl dark:bg-cream-100/5">{{ $method->isQris() ? '📱' : '🏦' }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-bold">{{ $method->name }}</span>
                            <span class="block truncate text-xs text-cocoa-500/70">
                                @if($method->isQris()) Scan QR bayar instan
                                @else {{ $method->account_number }} a.n. {{ $method->account_name }} @endif
                            </span>
                        </span>
                    </label>
                    @endforeach
                </div>
                @endif
                <button class="mt-5 w-full rounded-2xl bg-linear-to-r from-night-900 to-sage-600 dark:from-sage-500 dark:to-sage-600 py-4 font-bold text-white shadow-lg hover:opacity-90 active:scale-[0.99] transition">Buat Pesanan — <span id="btn-total">Rp {{ number_format($total, 0, ',', '.') }}</span></button>
                <p class="mt-2.5 text-center text-xs text-cocoa-500/70">Dengan memesan, kamu menyetujui syarat & ketentuan Shopedia.</p>
            </div>
        </form>

        <div class="h-fit rounded-3xl border border-line/70 bg-card p-6 dark:bg-night-800 dark:border-cream-100/15 lg:sticky lg:top-24">
            <h2 class="font-extrabold">Pesananmu ({{ $items->count() }})</h2>
            <div class="mt-3 max-h-64 overflow-auto space-y-2.5 text-sm pr-1">
                @foreach($items as $item)
                <div class="flex justify-between gap-3 rounded-2xl bg-milk-100 p-3 dark:bg-cream-100/5">
                    <span class="line-clamp-1">{{ $item['product']->name }} <span class="text-cocoa-500/70">×{{ $item['quantity'] }}</span></span>
                    <span class="font-bold whitespace-nowrap">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-3 space-y-1.5 text-sm">
                <div class="flex justify-between text-cocoa-500"><span>Subtotal</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-cocoa-500"><span>Ongkir (<span id="sum-service">Reguler</span>)</span><span id="sum-shipping">Rp 10.000</span></div>
            </div>
            <div class="mt-3 border-t border-dashed border-line dark:border-cream-100/15 pt-3 flex justify-between font-black"><span>Total</span><span class="text-gradient" id="sum-total">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const fmt = (n) => 'Rp ' + n.toLocaleString('id-ID');
    const subtotal = {{ (int) $total }};

    // --- Total dinamis mengikuti layanan terpilih ---
    const serviceInputs = document.querySelectorAll('input[name="shipping_service"]');
    const courierInput = document.getElementById('input-courier');
    const warning = document.getElementById('service-warning');

    function refreshTotal() {
        const checked = document.querySelector('input[name="shipping_service"]:checked');
        if (!checked) return;
        const cost = parseInt(checked.dataset.cost || '0', 10);
        const label = checked.closest('.service-option').querySelector('.font-bold').textContent;
        document.getElementById('sum-shipping').textContent = fmt(cost);
        document.getElementById('sum-service').textContent = label.split(' (')[0];
        document.getElementById('sum-total').textContent = fmt(subtotal + cost);
        document.getElementById('btn-total').textContent = fmt(subtotal + cost);

        // Tandai layanan yang tak tersedia untuk kurir terpilih
        let valid = true;
        document.querySelectorAll('.service-option').forEach((opt) => {
            const allowed = opt.dataset.couriers.split(',');
            const ok = allowed.includes(courierInput.value);
            opt.classList.toggle('opacity-40', !ok);
            if (opt.contains(checked) && !ok) valid = false;
        });
        warning.classList.toggle('hidden', valid);
    }
    serviceInputs.forEach((i) => i.addEventListener('change', refreshTotal));
    courierInput.addEventListener('change', refreshTotal);
    refreshTotal();

    // --- Peta pin lokasi (Leaflet + OpenStreetMap, gratis tanpa API key) ---
    const defLat = {{ $map['lat'] }}, defLng = {{ $map['lng'] }}, defZoom = {{ $map['zoom'] }};
    const latInput = document.getElementById('input-lat');
    const lngInput = document.getElementById('input-lng');
    const label = document.getElementById('pickup-label');
    const map = L.map('pickup-map').setView(
        [parseFloat(latInput.value) || defLat, parseFloat(lngInput.value) || defLng],
        defZoom
    );
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = null;
    function setPoint(lat, lng, text) {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        if (marker) marker.setLatLng([lat, lng]);
        else marker = L.marker([lat, lng], { draggable: true }).addTo(map).on('dragend', (e) => {
            const p = e.target.getLatLng();
            setPoint(p.lat, p.lng);
        });
        label.textContent = text || ('Titik: ' + lat.toFixed(5) + ', ' + lng.toFixed(5) + ' — bisa digeser.');
    }
    if (latInput.value && lngInput.value) setPoint(parseFloat(latInput.value), parseFloat(lngInput.value), 'Titik tersimpan, bisa digeser.');
    map.on('click', (e) => setPoint(e.latlng.lat, e.latlng.lng));
    setTimeout(() => map.invalidateSize(), 300);
})();
</script>
@endsection
