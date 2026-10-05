<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderNotifyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_notify_logs_and_redirects_to_whatsapp(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@shopedia.test')->first();
        $customer = User::where('email', 'customer@shopedia.test')->first();
        $product = Product::first();
        $method = PaymentMethod::where('slug', 'bca')->first();

        $order = Order::create([
            'user_id' => $customer->id,
            'payment_method_id' => $method->id,
            'total_price' => 100000,
            'status' => 'processing',
            'recipient_name' => 'Budi',
            'recipient_phone' => '08123456789',
            'shipping_address' => 'Jl. Test No. 1, Bandung',
            'paid_at' => now(),
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100000,
        ]);

        // Customer tidak boleh
        $this->actingAs($customer);
        $this->post(route('admin.orders.notify', $order))->assertForbidden();

        // Order belum lunas tidak bisa dikirimi struk
        $this->actingAs($admin);
        $order->update(['paid_at' => null, 'status' => 'awaiting_payment']);
        $this->post(route('admin.orders.notify', $order))->assertStatus(400);

        // Order lunas: tercatat + redirect ke wa.me
        $order->update(['paid_at' => now(), 'status' => 'processing']);
        $response = $this->post(route('admin.orders.notify', $order));
        $response->assertRedirect();
        $this->assertStringStartsWith('https://wa.me/628123456789', $response->headers->get('Location'));
        $this->assertDatabaseHas('notification_logs', [
            'order_id' => $order->id,
            'channel' => 'whatsapp',
            'recipient' => '628123456789',
        ]);
    }

    public function test_contact_page_loads(): void
    {
        $this->get(route('contact'))->assertOk()->assertSee('Hubungi Kami');
    }
}
