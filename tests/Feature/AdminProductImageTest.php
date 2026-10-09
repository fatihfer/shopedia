<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_replace_and_delete_product_image(): void
    {
        Storage::fake('public');
        $this->seed();

        $admin = User::where('email', 'admin@shopedia.test')->first();
        $category = Category::first();
        $this->actingAs($admin);

        // Buat produk dengan gambar
        $response = $this->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Produk Foto Baru',
            'description' => 'Deskripsi.',
            'price' => 100000,
            'stock' => 10,
            'image' => UploadedFile::fake()->image('foto.jpg'),
        ]);
        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('slug', 'produk-foto-baru')->firstOrFail();
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
        $oldPath = $product->image_path;

        // Halaman katalog menampilkan gambar
        $this->get(route('products.show', $product))->assertSee(asset('storage/' . $oldPath));

        // Ganti gambar: file lama terhapus
        $this->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Produk Foto Baru',
            'description' => 'Deskripsi.',
            'price' => 100000,
            'stock' => 10,
            'image' => UploadedFile::fake()->image('foto2.png'),
        ])->assertRedirect(route('admin.products.index'));

        $this->assertNotEquals($oldPath, $product->fresh()->image_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($product->fresh()->image_path);

        // Hapus produk: file ikut terhapus
        $path = $product->fresh()->image_path;
        $this->delete(route('admin.products.destroy', $product))->assertRedirect();
        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
