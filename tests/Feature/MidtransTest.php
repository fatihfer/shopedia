<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransTest extends TestCase
{
    use RefreshDatabase;

    private function signature(array $payload): string
    {
        config(['midtrans.server_key' => 'test-server-key']);

        return hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . 'test-server-key');
    }

    private function gatewayOrder(User $customer, Product $product, PaymentMethod $method): Order
    {
        $order = Order::create([
            'user_id' => $customer->id,
            'payment_method_id' => $method->id,
            'gateway_ref' => 'SHOPEDIA-TEST-1',
            'qr_string' => 'fake-qr-payload',
            'total_price' => 100000,
            'status' => 'awaiting_payment',
            'recipient_name' => 'Budi',
            'recipient_phone' => '08123456789',
            'shipping_address' => 'Jl. Test No. 1, Bandung',
            'expired_at' => now()->addMinutes(30),
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100000,
        ]);

        return $order;
    }

    public function test_webhook_settlement_marks_order_processing(): void
    {
        $this->seed();

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $product = Product::first();
        $method = PaymentMethod::where('slug', 'qris')->first();
        $order = $this->gatewayOrder($customer, $product, $method);

        $payload = [
            'order_id' => 'SHOPEDIA-TEST-1',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ];
        $payload['signature_key'] = $this->signature($payload);

        $this->postJson(route('midtrans.notify'), $payload)->assertOk();

        $order = $order->fresh();
        $this->assertEquals('processing', $order->status);
        $this->assertNotNull($order->paid_at);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $this->seed();

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $product = Product::first();
        $method = PaymentMethod::where('slug', 'qris')->first();
        $order = $this->gatewayOrder($customer, $product, $method);

        $this->postJson(route('midtrans.notify'), [
            'order_id' => 'SHOPEDIA-TEST-1',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'palsu',
        ])->assertStatus(400);

        $this->assertEquals('awaiting_payment', $order->fresh()->status);
    }

    public function test_webhook_expire_restores_stock(): void
    {
        $this->seed();

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $product = Product::first();
        $method = PaymentMethod::where('slug', 'qris')->first();
        $order = $this->gatewayOrder($customer, $product, $method);

        $stockBefore = $product->fresh()->stock;
        $product->decrement('stock', 1);

        $payload = [
            'order_id' => 'SHOPEDIA-TEST-1',
            'status_code' => '407',
            'gross_amount' => '100000.00',
            'transaction_status' => 'expire',
        ];
        $payload['signature_key'] = $this->signature($payload);

        $this->postJson(route('midtrans.notify'), $payload)->assertOk();

        $this->assertEquals('expired', $order->fresh()->status);
        $this->assertEquals($stockBefore, $product->fresh()->stock);
    }

    public function test_checkout_with_gateway_creates_qr_order(): void
    {
        config(['payment.demo_mode' => false]);
        $this->seed();

        $this->mock(MidtransService::class, function ($mock) {
            $mock->shouldReceive('chargeQris')->once()->andReturn(['qr_string' => 'mocked-qr', 'qr_image_url' => 'https://example.test/qr.png']);
        });

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $product = Product::where('stock', '>', 5)->first();
        $method = PaymentMethod::where('slug', 'qris')->first();
        $this->assertTrue($method->isGateway());

        $this->actingAs($customer);
        $this->post(route('cart.store', $product), ['quantity' => 1])->assertRedirect();
        $this->post(route('checkout.store'), [
            'recipient_name' => 'Budi',
            'recipient_phone' => '08123456789',
            'shipping_address' => 'Jl. Test No. 1, Bandung Barat',
            'courier' => 'jne',
            'shipping_service' => 'reguler',
            'payment_method_id' => $method->id,
        ])->assertRedirect(route('orders.show', Order::latest()->first()));

        $order = Order::latest()->first();
        $this->assertNotNull($order->gateway_ref);
        $this->assertEquals('mocked-qr', $order->qr_string);
        $this->assertEquals('https://example.test/qr.png', $order->qr_image_url);
        $this->assertEquals('awaiting_payment', $order->status);
        $this->assertTrue($order->isGateway());
    }

    public function test_checkout_gateway_failure_cancels_order_and_restores_stock(): void
    {
        config(['payment.demo_mode' => false]);
        $this->seed();

        $this->mock(MidtransService::class, function ($mock) {
            $mock->shouldReceive('chargeQris')->once()->andThrow(new \Exception('Midtrans down'));
        });

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $product = Product::where('stock', '>', 5)->first();
        $method = PaymentMethod::where('slug', 'qris')->first();
        $stockBefore = $product->fresh()->stock;

        $this->actingAs($customer);
        $this->post(route('cart.store', $product), ['quantity' => 1])->assertRedirect();
        $this->post(route('checkout.store'), [
            'recipient_name' => 'Budi',
            'recipient_phone' => '08123456789',
            'shipping_address' => 'Jl. Test No. 1, Bandung Barat',
            'courier' => 'jne',
            'shipping_service' => 'reguler',
            'payment_method_id' => $method->id,
        ])->assertRedirect(route('orders.index'));

        $order = Order::latest()->first();
        $this->assertEquals('cancelled', $order->status);
        $this->assertEquals($stockBefore, $product->fresh()->stock);
    }

    public function test_sync_payment_pulls_settlement_from_midtrans(): void
    {
        $this->seed();

        $this->mock(MidtransService::class, function ($mock) {
            $mock->makePartial();
            $mock->shouldReceive('fetchStatus')->once()->andReturn([
                'transaction_status' => 'settlement',
                'fraud_status' => 'accept',
            ]);
        });

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $product = Product::first();
        $method = PaymentMethod::where('slug', 'qris')->first();
        $order = $this->gatewayOrder($customer, $product, $method);

        $this->actingAs($customer);
        $response = $this->postJson(route('orders.sync-payment', $order));
        $response->assertOk()->assertJson(['status' => 'processing', 'paid' => true]);

        $order = $order->fresh();
        $this->assertEquals('processing', $order->status);
        $this->assertNotNull($order->paid_at);
    }

    public function test_sync_payment_keeps_pending_when_unpaid(): void
    {
        $this->seed();

        $this->mock(MidtransService::class, function ($mock) {
            $mock->makePartial();
            $mock->shouldReceive('fetchStatus')->once()->andReturn([
                'transaction_status' => 'pending',
                'fraud_status' => '',
            ]);
        });

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $product = Product::first();
        $method = PaymentMethod::where('slug', 'qris')->first();
        $order = $this->gatewayOrder($customer, $product, $method);

        $this->actingAs($customer);
        $this->postJson(route('orders.sync-payment', $order))
            ->assertOk()
            ->assertJson(['status' => 'awaiting_payment', 'paid' => false]);

        $this->assertEquals('awaiting_payment', $order->fresh()->status);
    }
}
