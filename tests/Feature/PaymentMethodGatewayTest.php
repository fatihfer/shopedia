<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_created_qris_defaults_to_midtrans_gateway(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@shopedia.test')->first();
        $this->actingAs($admin);

        $this->post(route('admin.payment-methods.store'), [
            'name' => 'QRIS Baru',
            'type' => 'qris',
            'is_active' => true,
        ])->assertRedirect(route('admin.payment-methods.index'));

        $method = PaymentMethod::where('slug', 'qris-baru')->firstOrFail();
        $this->assertTrue($method->isGateway());
    }

    public function test_admin_can_choose_manual_qris_and_bank_is_always_manual(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@shopedia.test')->first();
        $this->actingAs($admin);

        $this->post(route('admin.payment-methods.store'), [
            'name' => 'QRIS Manual',
            'type' => 'qris',
            'gateway' => 'manual',
            'is_active' => true,
        ])->assertRedirect();

        $manual = PaymentMethod::where('slug', 'qris-manual')->firstOrFail();
        $this->assertFalse($manual->isGateway());

        // Bank dipaksa manual walau dikirim gateway=midtrans
        $this->post(route('admin.payment-methods.store'), [
            'name' => 'Bank Tes',
            'type' => 'bank_transfer',
            'gateway' => 'midtrans',
            'account_number' => '111222333',
            'is_active' => true,
        ])->assertRedirect();

        $bank = PaymentMethod::where('slug', 'bank-tes')->firstOrFail();
        $this->assertFalse($bank->isGateway());
        $this->assertEquals('manual', $bank->gateway);
    }
}
