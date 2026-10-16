<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoPayTest extends TestCase
{
    use RefreshDatabase;

    private function checkoutDemo(User $customer, Product $product, PaymentMethod $method): Order
    {
        config(['payment.demo_mode' => true]);

        $this->actingAs($customer);
        $this->post(route('cart.store', $product), ['quantity' => 1])->assertRedirect();
        $this->post(route('checkout.store'), [
            'recipient_name' => 'Budi',
            'recipient_phone' => '08123456789',
            'shipping_address' => 'Jl. Test No. 1, Bandung Barat',
            'courier' => 'jne',
            'shipping_service' => 'reguler',
            'payment_method_id' => $method->id,
        ])->assertRedirect();

        return Order::latest()->first();
    }

    public function test_demo_checkout_creates_pay_link_without_midtrans(): void
    {
        $this->seed();

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $product = Product::where('stock', '>', 5)->first();
        $method = PaymentMethod::where('slug', 'qris')->first();

        $order = $this->checkoutDemo($customer, $product, $method);

        $this->assertNotNull($order->pay_token);
        $this->assertEquals(route('pay.show', ['token' => $order->pay_token]), $order->qr_string);
        $this->assertStringStartsWith('DEMO-', $order->gateway_ref);
        $this->assertEquals('awaiting_payment', $order->status);
        $this->get(route('orders.show', $order))->assertSee('kamera HP');
    }

    public function test_pay_page_confirm_marks_order_processing(): void
    {
        $this->seed();

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $product = Product::where('stock', '>', 5)->first();
        $method = PaymentMethod::where('slug', 'qris')->first();

        $order = $this->checkoutDemo($customer, $product, $method);

        // Tanpa login pun bisa (token adalah kuncinya)
        auth()->logout();
        $this->get(route('pay.show', $order->pay_token))->assertOk()->assertSee('Rp ' . number_format($order->total_price, 0, ',', '.'));

        $this->post(route('pay.confirm', $order->pay_token))
            ->assertRedirect(route('pay.show', $order->pay_token));

        $order = $order->fresh();
        $this->assertEquals('processing', $order->status);
        $this->assertNotNull($order->paid_at);

        // Bayar dua kali ditolak
        $this->post(route('pay.confirm', $order->pay_token))->assertStatus(400);
    }

    public function test_pay_page_rejects_bad_and_expired_tokens(): void
    {
        $this->seed();

        $this->get('/bayar/token-salah')->assertNotFound();
        $this->get('/bayar/pendek')->assertNotFound();

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $product = Product::where('stock', '>', 5)->first();
        $method = PaymentMethod::where('slug', 'qris')->first();

        $order = $this->checkoutDemo($customer, $product, $method);
        $order->update(['expired_at' => now()->subMinute()]);

        auth()->logout();
        $this->get(route('pay.show', $order->pay_token))->assertOk();
        $this->assertEquals('expired', $order->fresh()->status);
        $this->post(route('pay.confirm', $order->pay_token))->assertStatus(400);
    }
}
