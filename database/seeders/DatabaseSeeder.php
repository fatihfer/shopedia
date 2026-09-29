<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@shopedia.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'customer@shopedia.test'],
            [
                'name' => 'Customer',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        User::factory(8)->create(['role' => 'customer']);

        foreach ($this->categories() as $category) {
            $model = Category::updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );

            foreach ($category['products'] as $product) {
                Product::updateOrCreate(
                    ['slug' => $product['slug']],
                    [
                        'category_id' => $model->id,
                        'name' => $product['name'],
                        'description' => $product['description'],
                        'price' => $product['price'],
                        'stock' => $product['stock'],
                        'image_path' => null,
                    ]
                );
            }
        }
    }

    /** @return array<int, array{slug: string, name: string, products: array<int, array{slug: string, name: string, description: string, price: int, stock: int}>}> */
    private function categories(): array
    {
        return [
            [
                'slug' => 'sneakers',
                'name' => 'Sneakers',
                'products' => [
                    [
                        'slug' => 'nike-air-zoom-pegasus-40',
                        'name' => 'Nike Air Zoom Pegasus 40',
                        'description' => 'Sepatu lari pria dengan bantalan Zoom Air responsif dan upper mesh yang breathable. Nyaman untuk lari harian 5–10K.',
                        'price' => 1899000,
                        'stock' => 24,
                    ],
                    [
                        'slug' => 'adidas-grand-court-base',
                        'name' => 'Adidas Grand Court Base',
                        'description' => 'Sneakers kasual klasik dengan siluet tenis timeless. Upper sintetis halus dan outsole karet anti-slip, cocok untuk daily wear.',
                        'price' => 899000,
                        'stock' => 37,
                    ],
                    [
                        'slug' => 'vans-old-skool-classic',
                        'name' => 'Vans Old Skool Classic Black White',
                        'description' => 'Ikon skate dengan sidestripe putih legendaris. Kanvas dan suede yang awet, waffle outsole dengan grip maksimal.',
                        'price' => 1099000,
                        'stock' => 18,
                    ],
                    [
                        'slug' => 'converse-chuck-taylor-70s',
                        'name' => 'Converse Chuck Taylor 70s Hi Black',
                        'description' => 'Edisi premium Chuck Taylor dengan kanvas lebih tebal, cushioning OrthoLite, dan detail vintage 70-an.',
                        'price' => 1199000,
                        'stock' => 4,
                    ],
                    [
                        'slug' => 'puma-smash-v2-putih',
                        'name' => 'Puma Smash v2 Putih',
                        'description' => 'Sneakers court-clean dengan upper kulit sintetis dan branding Puma minimalis. Ringan dan gampang dipadukan.',
                        'price' => 799000,
                        'stock' => 29,
                    ],
                ],
            ],
            [
                'slug' => 'skincare',
                'name' => 'Skincare',
                'products' => [
                    [
                        'slug' => 'somethinc-niacinamide-10-serum',
                        'name' => 'Somethinc Niacinamide 10% + Moisture Sabi Beet Serum 20ml',
                        'description' => 'Serum niacinamide 10% untuk mencerahkan, menyamarkan noda hitam, dan mengontrol minyak. Cocok untuk semua jenis kulit.',
                        'price' => 149000,
                        'stock' => 52,
                    ],
                    [
                        'slug' => 'avoskin-miraculous-retinol-toner',
                        'name' => 'Avoskin Miraculous Retinol Toner 100ml',
                        'description' => 'Exfoliating toner dengan retinol dan niacinamide untuk tekstur kulit lebih halus dan pori tersamar. Pakai 2–3x seminggu.',
                        'price' => 199000,
                        'stock' => 31,
                    ],
                    [
                        'slug' => 'wardah-uv-shield-sunscreen',
                        'name' => 'Wardah UV Shield Essential Sunscreen Gel SPF 50 40ml',
                        'description' => 'Sunscreen gel ringan SPF 50 PA++++ tanpa whitecast. Tahan keringat, nyaman dipakai sehari-hari di bawah makeup.',
                        'price' => 78000,
                        'stock' => 64,
                    ],
                    [
                        'slug' => 'skintific-5x-ceramide-moisturizer',
                        'name' => 'Skintific 5X Ceramide Barrier Moisturizer 30g',
                        'description' => 'Pelembap 5 jenis ceramide untuk memperbaiki skin barrier dalam 3 hari. Tekstur gel-cream cepat meresap.',
                        'price' => 169000,
                        'stock' => 43,
                    ],
                    [
                        'slug' => 'facetology-low-ph-cleanser',
                        'name' => 'Facetology Triple Care Low pH Cleanser 100ml',
                        'description' => 'Facial wash gentle pH rendah dengan salicylic acid dan tea tree. Membersihkan tanpa bikin kulit ketarik.',
                        'price' => 89000,
                        'stock' => 0,
                    ],
                ],
            ],
            [
                'slug' => 'kopi',
                'name' => 'Kopi',
                'products' => [
                    [
                        'slug' => 'kopi-susu-gula-aren-1-liter',
                        'name' => 'Kopi Susu Gula Aren 1 Liter',
                        'description' => 'Espresso double shot, susu full cream, dan gula aren asli. Disangrai medium-dark, creamy dan tidak eneg. Tahan 3 hari di kulkas.',
                        'price' => 65000,
                        'stock' => 40,
                    ],
                    [
                        'slug' => 'arabika-gayo-wine-process-200g',
                        'name' => 'Biji Kopi Arabika Gayo Wine Process 200g',
                        'description' => 'Single origin Gayo dengan proses wine, notes fruity dan winey yang unik. Sangrai medium, tersedia whole bean atau giling.',
                        'price' => 98000,
                        'stock' => 27,
                    ],
                    [
                        'slug' => 'robusta-lampung-500g',
                        'name' => 'Biji Kopi Robusta Lampung 500g',
                        'description' => 'Robusta bold dengan crema tebal, favorit untuk espresso dan kopi tubruk. Sangrai dark, pahit mantap.',
                        'price' => 75000,
                        'stock' => 35,
                    ],
                    [
                        'slug' => 'cold-brew-bottle-250ml',
                        'name' => 'Cold Brew Black 250ml',
                        'description' => 'Kopi diseduh dingin 18 jam dari biji arabika Toraja. Smooth, low acidity, dan menyegarkan. Siap minum.',
                        'price' => 32000,
                        'stock' => 58,
                    ],
                    [
                        'slug' => 'drip-coffee-gayo-10pcs',
                        'name' => 'Drip Coffee Gayo Isi 10pcs',
                        'description' => 'Kopi drip praktis, tinggal seduh air panas 180ml. Cocok untuk di kantor atau travelling. Varian Gayo dan Toraja.',
                        'price' => 55000,
                        'stock' => 3,
                    ],
                ],
            ],
            [
                'slug' => 'elektronik',
                'name' => 'Elektronik',
                'products' => [
                    [
                        'slug' => 'soundcore-r50i-tws',
                        'name' => 'Soundcore R50i True Wireless Earbuds',
                        'description' => 'TWS dengan driver 10mm, bass mantap, dan baterai total 30 jam. Bluetooth 5.3 stabil dengan latency rendah untuk gaming.',
                        'price' => 349000,
                        'stock' => 26,
                    ],
                    [
                        'slug' => 'powerbank-robot-10000mah',
                        'name' => 'Powerbank Robot 10000mAh 22.5W Fast Charging',
                        'description' => 'Powerbank 10000mAh dengan fast charging 22.5W dan display LED. Dua output USB + Type-C, aman dibawa di kabin.',
                        'price' => 279000,
                        'stock' => 33,
                    ],
                    [
                        'slug' => 'lampu-led-philips-9w-2pcs',
                        'name' => 'Lampu LED Philips 9 Watt Isi 2pcs',
                        'description' => 'Lampu LED hemat energi 9W setara 60W pijar, cahaya putih 6500K. Umur hingga 15.000 jam pemakaian.',
                        'price' => 89000,
                        'stock' => 71,
                    ],
                    [
                        'slug' => 'kipas-mini-usb-jisulife',
                        'name' => 'Kipas Mini Portable USB 4000mAh',
                        'description' => 'Kipas genggam 5 kecepatan dengan baterai 4000mAh tahan hingga 12 jam. Lipat 180 derajat, ada display digital.',
                        'price' => 159000,
                        'stock' => 22,
                    ],
                    [
                        'slug' => 'kabel-type-c-60w-1m',
                        'name' => 'Kabel Data Type-C to Type-C 60W 1 Meter',
                        'description' => 'Kabel nylon braided 60W untuk fast charging dan transfer data 480Mbps. Konektor reinforced anti-putus.',
                        'price' => 59000,
                        'stock' => 84,
                    ],
                ],
            ],
            [
                'slug' => 'fashion',
                'name' => 'Fashion',
                'products' => [
                    [
                        'slug' => 'kaos-oversize-cotton-combed-hitam',
                        'name' => 'Kaos Oversize Cotton Combed 24s Hitam',
                        'description' => 'Kaos oversize bahan cotton combed 24s adem dan tidak nerawang. Sablon plastisol premium, tersedia S–XXL.',
                        'price' => 99000,
                        'stock' => 47,
                    ],
                    [
                        'slug' => 'hoodie-fleece-abu',
                        'name' => 'Hoodie Fleece Abu Misty',
                        'description' => 'Hoodie fleece cotton tebal 330gsm dengan saku kanguru dan tali adjustable. Hangat dan nyaman untuk harian.',
                        'price' => 179000,
                        'stock' => 19,
                    ],
                    [
                        'slug' => 'celana-jeans-slimfit-navy',
                        'name' => 'Celana Jeans Slimfit Stretch Navy',
                        'description' => 'Jeans slimfit denim stretch 12oz yang lentur. Warna navy solid, potongan rapi cocok untuk kasual maupun semi-formal.',
                        'price' => 249000,
                        'stock' => 16,
                    ],
                    [
                        'slug' => 'tas-selempang-canvas',
                        'name' => 'Tas Selempang Canvas Waterproof',
                        'description' => 'Sling bag kanvas waterproof muat HP, dompet, dan powerbank. Tali adjustable dan resleting YKK anti-macet.',
                        'price' => 129000,
                        'stock' => 28,
                    ],
                    [
                        'slug' => 'topi-baseball-caps-hitam',
                        'name' => 'Topi Baseball Caps Polos Hitam',
                        'description' => 'Topi 6-panel bahan twill premium dengan strap adjustable. Bordir minimalis, one size untuk dewasa.',
                        'price' => 69000,
                        'stock' => 39,
                    ],
                ],
            ],
            [
                'slug' => 'rumah-tangga',
                'name' => 'Rumah Tangga',
                'products' => [
                    [
                        'slug' => 'tumbler-stainless-750ml',
                        'name' => 'Tumbler Stainless Steel 750ml',
                        'description' => 'Tumbler vacuum double-wall, dingin 24 jam dan panas 12 jam. Free sedotan stainless dan sikat pembersih.',
                        'price' => 139000,
                        'stock' => 45,
                    ],
                    [
                        'slug' => 'reed-diffuser-vanilla-50ml',
                        'name' => 'Reed Diffuser Vanilla 50ml',
                        'description' => 'Pengharum ruangan aroma vanilla hangat, tahan 4–6 minggu. Termasuk 5 stick rattan dan cocok untuk kamar 20m².',
                        'price' => 85000,
                        'stock' => 21,
                    ],
                    [
                        'slug' => 'kotak-penyimpanan-lipat-35l',
                        'name' => 'Kotak Penyimpanan Lipat 35L',
                        'description' => 'Storage box lipat kapasitas 35L dengan tutup dan jendela transparan. Kuat menahan 20kg, hemat tempat saat dilipat.',
                        'price' => 95000,
                        'stock' => 34,
                    ],
                    [
                        'slug' => 'lampu-tidur-led-touch',
                        'name' => 'Lampu Tidur LED Touch 3 Warna',
                        'description' => 'Lampu meja touch control 3 suhu warna dengan dimmer. Baterai isi ulang Type-C, tahan 10 jam.',
                        'price' => 119000,
                        'stock' => 17,
                    ],
                    [
                        'slug' => 'tas-belanja-lipat-kanvas',
                        'name' => 'Tas Belanja Lipat Kanvas Motif',
                        'description' => 'Tote bag kanvas tebal yang bisa dilipat jadi pouch kecil. Kuat menahan 15kg belanjaan, motif minimalis estetik.',
                        'price' => 49000,
                        'stock' => 56,
                    ],
                ],
            ],
        ];
    }
}
