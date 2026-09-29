<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    private const DESCRIPTIONS = [
        'Produk original dengan kualitas terjamin. Cocok untuk pemakaian sehari-hari, pengiriman cepat dari Jakarta.',
        'Bahan premium dan jahitan rapi. Sudah terjual ratusan pcs dengan rating 4.9 dari pembeli.',
        'Dibuat dari material pilihan yang awet dan nyaman dipakai. Garansi 7 hari jika ada cacat produksi.',
        'Best seller di kategorinya. Stok terbatas, dikemas aman dengan bubble wrap dan kardus tebal.',
    ];
    private const POOL = [
        ['Kaos Polos Cotton Combed 30s Putih', 79000],
        ['Hoodie Fleece Hitam', 179000],
        ['Kopi Susu Gula Aren 250ml', 22000],
        ['Biji Kopi Arabika Toraja 200g', 95000],
        ['Sunscreen Gel SPF 50 40ml', 78000],
        ['Serum Niacinamide 10% 20ml', 149000],
        ['Sneakers Kasual Putih', 499000],
        ['Tas Selempang Canvas', 129000],
        ['Tumbler Stainless 500ml', 99000],
        ['Powerbank 10000mAh Fast Charging', 279000],
        ['TWS Earbuds Bluetooth', 249000],
        ['Lampu LED 9 Watt', 45000],
        ['Celana Jeans Slimfit', 249000],
        ['Topi Baseball Polos', 69000],
        ['Reed Diffuser Lavender 50ml', 85000],
        ['Kotak Penyimpanan Lipat 35L', 95000],
        ['Kipas Mini USB Portable', 159000],
        ['Kabel Type-C 60W 1 Meter', 59000],
        ['Cold Brew Black 250ml', 32000],
        ['Drip Coffee Isi 10pcs', 55000],
    ];

    public function definition(): array
    {
        [$name, $price] = fake()->unique()->randomElement(self::POOL);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(6),
            'description' => fake()->randomElement(self::DESCRIPTIONS),
            'price' => $price,
            'stock' => fake()->numberBetween(5, 60),
            'image_path' => null,
        ];
    }
}
