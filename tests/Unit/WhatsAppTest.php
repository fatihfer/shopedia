<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Support\WhatsApp;
use PHPUnit\Framework\TestCase;

class WhatsAppTest extends TestCase
{
    public function test_normalize_phone(): void
    {
        $this->assertSame('628123456789', WhatsApp::normalizePhone('08123456789'));
        $this->assertSame('628123456789', WhatsApp::normalizePhone('+628123456789'));
        $this->assertSame('628123456789', WhatsApp::normalizePhone('628123456789'));
        $this->assertSame('628123456789', WhatsApp::normalizePhone('0812-345-6789'));
        $this->assertNull(WhatsApp::normalizePhone(null));
        $this->assertNull(WhatsApp::normalizePhone('abc'));
        $this->assertNull(WhatsApp::normalizePhone('123'));
    }

    public function test_wa_me_url(): void
    {
        $this->assertSame('https://wa.me/6281234567890', WhatsApp::waMeUrl('6281234567890'));
        $this->assertSame(
            'https://wa.me/6281234567890?text=' . rawurlencode('Halo #12'),
            WhatsApp::waMeUrl('6281234567890', 'Halo #12')
        );
    }

    public function test_receipt_message_contains_order_data(): void
    {
        $order = new Order([
            'total_price' => 110000,
            'shipping_cost' => 10000,
        ]);
        $order->id = 12;
        $order->setRelation('items', collect());
        $order->setRelation('paymentMethod', null);

        $message = WhatsApp::receiptMessage($order);

        $this->assertStringContainsString('#12', $message);
        $this->assertStringContainsString('Rp110.000', $message);
        $this->assertStringContainsString('Rp10.000', $message);
    }
}
