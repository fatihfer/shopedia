<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_product_that_is_referenced_by_order_items(): void
    {
        Storage::fake('public');
        $this->seed();

        $admin = User::where('email', 'admin@shopedia.test')->firstOrFail();
        $customer = User::where('email', 'customer@shopedia.test')->firstOrFail();
        $product = Product::firstOrFail();
        $method = PaymentMethod::where('slug', 'bca')->firstOrFail();

        $order = Order::create([
            'user_id' => $customer->id,
            'payment_method_id' => $method->id,
            'total_price' => $product->price,
            'status' => 'processing',
            'recipient_name' => 'Budi',
            'recipient_phone' => '08123456789',
            'shipping_address' => 'Jl. Test No. 1, Bandung',
            'paid_at' => now(),
        ]);

        $item = $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'price' => $product->price,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'product_id' => null,
            'product_name' => $product->name,
        ]);

        // Riwayat pesanan tetap menampilkan nama produk aslinya.
        $this->actingAs($customer)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee($product->name);
    }
}
