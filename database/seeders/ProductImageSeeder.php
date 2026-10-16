<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Isi foto produk dengan prioritas:
 *   1. Foto yang sudah ada (upload admin) — tidak disentuh.
 *   2. Foto asli di database/seeders/photos/{slug}.jpg|jpeg|png|webp
 *      (ikut ter-commit ke git & image Docker, jadi ada juga di server).
 *   3. Fallback: generate placeholder SVG per kategori.
 *
 * Idempoten, aman dijalankan ulang via php artisan db:seed --force.
 */
class ProductImageSeeder extends Seeder
{
    /** Warna gradien per slug kategori. */
    private const COLORS = [
        'sneakers' => ['#4338ca', '#7c3aed'],
        'skincare' => ['#db2777', '#f9a8d4'],
        'kopi' => ['#92400e', '#d97706'],
        'elektronik' => ['#0369a1', '#22d3ee'],
        'fashion' => ['#be123c', '#fb7185'],
        'rumah-tangga' => ['#047857', '#34d399'],
    ];

    public function run(): void
    {
        $disk = Storage::disk('public');

        $products = Product::with('category')->get();

        foreach ($products as $product) {
            $current = $product->image_path;
            $currentExists = $current && $disk->exists($current);
            $isPlaceholder = (bool) ($current && str_ends_with($current, '.svg'));
            $seedPhoto = $this->seedPhoto($product->slug);

            if ($seedPhoto) {
                // Foto asli mengalahkan placeholder SVG, tapi tidak
                // mengalahkan foto upload admin (nama file acak).
                $path = "products/{$product->slug}.{$seedPhoto['ext']}";
                $freshBytes = file_get_contents($seedPhoto['file']);
                $shouldCopy = ! $disk->exists($path)
                    || $isPlaceholder
                    || ($current === $path && $disk->get($path) !== $freshBytes);

                if ($shouldCopy) {
                    $disk->put($path, $freshBytes);

                    if ($isPlaceholder && $current !== $path) {
                        $disk->delete($current);
                    }

                    if ($current !== $path) {
                        $product->update(['image_path' => $path]);
                    }
                }

                continue;
            }

            // Tanpa foto seed: pertahankan file yang ada, atau fallback SVG.
            if ($currentExists) {
                continue;
            }

            $path = "products/{$product->slug}.svg";

            if (! $disk->exists($path)) {
                $disk->put($path, $this->svg($product->name, $product->category->slug ?? ''));
            }

            if ($current !== $path) {
                $product->update(['image_path' => $path]);
            }
        }

        $this->command?->info(Product::whereNotNull('image_path')->count() . ' produk memiliki gambar.');
    }

    /** @return array{file: string, ext: string}|null */
    private function seedPhoto(string $slug): ?array
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $file = database_path("seeders/photos/{$slug}.{$ext}");

            if (is_file($file)) {
                return ['file' => $file, 'ext' => $ext === 'jpeg' ? 'jpg' : $ext];
            }
        }

        return null;
    }

    private function svg(string $name, string $categorySlug): string
    {
        [$from, $to] = self::COLORS[$categorySlug] ?? ['#5a6854', '#a9b8a3'];
        $initial = mb_strtoupper(mb_substr(trim($name), 0, 1) ?: 'S');
        $label = htmlspecialchars(mb_strtoupper(mb_substr($categorySlug ?: 'shopedia', 0, 18)), ENT_QUOTES);

        return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800">
              <defs>
                <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0" stop-color="{$from}"/>
                  <stop offset="1" stop-color="{$to}"/>
                </linearGradient>
              </defs>
              <rect width="800" height="800" fill="url(#g)"/>
              <circle cx="640" cy="140" r="220" fill="#ffffff" opacity="0.12"/>
              <circle cx="120" cy="680" r="260" fill="#000000" opacity="0.10"/>
              <circle cx="120" cy="680" r="180" fill="#ffffff" opacity="0.08"/>
              <text x="400" y="470" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="360" font-weight="900" fill="#ffffff" opacity="0.55">{$initial}</text>
              <rect x="0" y="690" width="800" height="110" fill="#000000" opacity="0.25"/>
              <text x="400" y="738" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="34" font-weight="700" letter-spacing="8" fill="#ffffff">{$label}</text>
              <text x="400" y="772" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="22" letter-spacing="4" fill="#ffffff" opacity="0.8">SHOPEEDIA • 100% ORIGINAL</text>
            </svg>
            SVG;
    }
}
