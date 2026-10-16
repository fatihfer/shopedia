<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\ProductImageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_seeder_generates_svg_and_updates_db(): void
    {
        Storage::fake('public');
        $this->seed();

        $this->assertEquals(0, Product::whereNull('image_path')->count());

        $product = Product::first();
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);

        $contents = Storage::disk('public')->get($product->image_path);
        $this->assertStringStartsWith('<svg', ltrim($contents));

        // Idempoten: seed ulang tidak menambah file & tidak menimpa foto admin
        $product->update(['image_path' => 'products/custom.jpg']);
        Storage::disk('public')->put('products/custom.jpg', 'fake');
        $this->seed();

        $this->assertEquals('products/custom.jpg', $product->fresh()->image_path);
    }

    public function test_real_photo_from_seed_folder_is_used(): void
    {
        Storage::fake('public');
        $this->seed();

        $product = Product::first();
        $photoDir = database_path('seeders/photos');
        @mkdir($photoDir, 0755, true);
        $photoFile = $photoDir . '/' . $product->slug . '.jpg';
        file_put_contents($photoFile, 'fake-jpg-bytes');

        try {
            // Reset ke placeholder dulu, lalu seed ulang
            $product->update(['image_path' => null]);
            $this->seed();

            $product = $product->fresh();
            $this->assertStringEndsWith('.jpg', $product->image_path);
            Storage::disk('public')->assertExists($product->image_path);
            $this->assertSame('fake-jpg-bytes', Storage::disk('public')->get($product->image_path));
        } finally {
            @unlink($photoFile);
        }
    }

    public function test_real_photo_replaces_svg_placeholder_but_not_admin_upload(): void
    {
        Storage::fake('public');
        $this->seed();

        $product = Product::first();
        $photoDir = database_path('seeders/photos');
        @mkdir($photoDir, 0755, true);
        $photoFile = $photoDir . '/' . $product->slug . '.jpg';
        file_put_contents($photoFile, 'real-photo');

        // Simulasi kondisi sekarang: produk menunjuk placeholder SVG yang ada
        Storage::disk('public')->put("products/{$product->slug}.svg", '<svg></svg>');
        $product->update(['image_path' => "products/{$product->slug}.svg"]);

        try {
            $this->seed();

            $product = $product->fresh();
            $this->assertStringEndsWith('.jpg', $product->image_path);
            $this->assertSame('real-photo', Storage::disk('public')->get($product->image_path));
            Storage::disk('public')->assertMissing("products/{$product->slug}.svg");

            // Foto upload admin (nama acak) tidak boleh tertimpa
            Storage::disk('public')->put('products/random-admin.jpg', 'admin-photo');
            $product->update(['image_path' => 'products/random-admin.jpg']);
            $this->seed();

            $this->assertEquals('products/random-admin.jpg', $product->fresh()->image_path);
            $this->assertSame('admin-photo', Storage::disk('public')->get('products/random-admin.jpg'));
        } finally {
            @unlink($photoFile);
        }
    }
}
