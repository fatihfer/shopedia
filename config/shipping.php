<?php

// Ongkir flat untuk demo (bukan tarif asli ekspedisi).
return [
    'couriers' => [
        'jne' => 'JNE',
        'jnt' => 'J&T Express',
        'sicepat' => 'SiCepat',
        'anteraja' => 'AnterAja',
        'gosend' => 'GoSend',
    ],

    'services' => [
        'hemat' => ['label' => 'Hemat (4–5 hari)', 'cost' => 8000, 'couriers' => ['jne', 'jnt', 'sicepat', 'anteraja']],
        'reguler' => ['label' => 'Reguler (2–3 hari)', 'cost' => 10000, 'couriers' => ['jne', 'jnt', 'sicepat', 'anteraja']],
        'express' => ['label' => 'Express (1 hari)', 'cost' => 25000, 'couriers' => ['jne', 'jnt', 'sicepat', 'anteraja', 'gosend']],
        'sameday' => ['label' => 'Same Day (8 jam)', 'cost' => 30000, 'couriers' => ['gosend']],
    ],

    // Titik tengah peta (Bandung)
    'map_default' => ['lat' => -6.9175, 'lng' => 107.6191, 'zoom' => 12],
];
