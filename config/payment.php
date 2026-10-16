<?php

return [
    // Mode demo: QR berisi link bayar milik sendiri (tanpa Midtrans).
    // Cocok untuk sidang/demo. Produksi: set false agar pakai QRIS Midtrans.
    'demo_mode' => (bool) env('PAYMENT_DEMO_MODE', false),

    'demo_expiry_minutes' => 30,
];
