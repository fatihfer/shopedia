<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcommerceTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_cart_checkout_admin_flow(): void
    {
        $this->seed();

        // 1. Homepage & product list
        $this->get('/')->assertOk();
        $this->get(route('products.index'))->assertOk();

        $product = Product::where('stock', '>', 5)->first();
        $this->assertNotNull($product);

        // 2. Product detail
        $this->get(route('products.show', $product))->assertOk()->assertSee($product->name);

        // 3. Guest add to cart (session)
        $this->post(route('cart.store', $product), ['quantity' => 2])
            ->assertRedirect(route('cart.index'));
        $this->get(route('cart.index'))->assertOk()->assertSee($product->name);

        // 4. Checkout requires auth
        $this->get(route('checkout.index'))->assertRedirect(route('login'));

        // 5. Login as customer & checkout
        $customer = User::where('email', 'customer@shopedia.test')->first();
        $this->actingAs($customer);

        $stockBefore = $product->fresh()->stock;

        $this->get(route('checkout.index'))->assertOk();
        $this->post(route('checkout.store'), ['shipping_address' => 'Jl. Test No. 123, Jakarta 12345, HP 0812'])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['user_id' => $customer->id]);
        $order = $customer->orders()->latest()->first();
        $this->assertEquals(2 * (float) $product->price, (float) $order->total_price);
        $this->assertEquals($stockBefore - 2, $product->fresh()->stock);

        // 6. Customer sees own order, cannot see others
        $this->get(route('orders.index'))->assertOk();
        $this->get(route('orders.show', $order))->assertOk();

        // 7. Customer cannot access admin
        $this->get(route('admin.dashboard'))->assertForbidden();

        // 8. Admin can access dashboard + update order
        $admin = User::where('email', 'admin@shopedia.test')->first();
        $this->actingAs($admin);
        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.products.index'))->assertOk();
        $this->patch(route('admin.orders.update', $order), ['status' => 'processing'])->assertRedirect();
        $this->assertEquals('processing', $order->fresh()->status);
    }
}
