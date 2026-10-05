<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EcommerceTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_cart_checkout_payment_admin_flow(): void
    {
        Storage::fake('public');
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

        // 5. Login as customer & checkout dengan metode bayar
        $customer = User::where('email', 'customer@shopedia.test')->first();
        $this->actingAs($customer);

        $stockBefore = $product->fresh()->stock;
        $method = PaymentMethod::where('slug', 'bca')->first();
        $this->assertNotNull($method);

        $this->get(route('checkout.index'))->assertOk()->assertSee('BCA')->assertSee('Data Penerima');
        $this->post(route('checkout.store'), [
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '08123456789',
            'shipping_address' => 'Jl. Test No. 123, Jakarta 12345, HP 0812',
            'courier' => 'jne',
            'shipping_service' => 'reguler',
            'payment_method_id' => $method->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', ['user_id' => $customer->id]);
        $order = $customer->orders()->latest()->first();
        $expectedTotal = 2 * (float) $product->price + 10000;
        $this->assertEquals($expectedTotal, (float) $order->total_price);
        $this->assertEquals(10000.0, (float) $order->shipping_cost);
        $this->assertEquals('Budi Santoso', $order->recipient_name);
        $this->assertEquals($stockBefore - 2, $product->fresh()->stock);
        $this->assertEquals('awaiting_payment', $order->status);
        $this->assertNotNull($order->expired_at);

        // 6. Upload bukti pembayaran
        $this->get(route('orders.show', $order))->assertOk()->assertSee('Bayar Pesanan');
        $this->post(route('orders.proof', $order), [
            'proof' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertRedirect();
        $this->assertEquals('awaiting_verification', $order->fresh()->status);
        Storage::disk('public')->assertExists($order->fresh()->payment_proof);

        // 7. Customer sees own order, cannot see others
        $this->get(route('orders.index'))->assertOk();
        $this->get(route('orders.show', $order))->assertOk();

        // 8. Customer cannot access admin
        $this->get(route('admin.dashboard'))->assertForbidden();

        // 9. Admin verifikasi: tolak dulu, lalu terima
        $admin = User::where('email', 'admin@shopedia.test')->first();
        $this->actingAs($admin);
        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.orders.index', ['status' => 'awaiting_verification']))->assertOk();
        $this->patch(route('admin.orders.update', $order), [
            'action' => 'reject',
            'rejection_reason' => 'Nominal kurang',
        ])->assertRedirect();
        $this->assertEquals('awaiting_payment', $order->fresh()->status);
        $this->assertEquals('Nominal kurang', $order->fresh()->rejection_reason);

        $this->actingAs($customer);
        $this->post(route('orders.proof', $order), [
            'proof' => UploadedFile::fake()->image('bukti2.jpg'),
        ])->assertRedirect();

        $this->actingAs($admin);
        $this->patch(route('admin.orders.update', $order), ['action' => 'accept'])->assertRedirect();
        $order = $order->fresh();
        $this->assertEquals('processing', $order->status);
        $this->assertNotNull($order->paid_at);

        // 10. Order kadaluarsa mengembalikan stok
        $expired = $customer->orders()->create([
            'payment_method_id' => $method->id,
            'total_price' => 10000,
            'status' => 'awaiting_payment',
            'shipping_address' => 'Jl. Kadaluarsa No. 1',
            'expired_at' => now()->subHour(),
        ]);
        $expired->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 10000,
        ]);
        $product->decrement('stock', 1);
        $stockBeforeExpire = $product->fresh()->stock;

        $this->actingAs($customer);
        $this->get(route('orders.show', $expired))->assertOk();
        $this->assertEquals('expired', $expired->fresh()->status);
        $this->assertEquals($stockBeforeExpire + 1, $product->fresh()->stock);
    }
}
