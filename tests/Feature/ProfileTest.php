<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_edit_and_update(): void
    {
        $this->seed();

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $this->actingAs($customer);

        $this->get(route('profile.edit'))->assertOk()->assertSee($customer->name);

        $this->put(route('profile.update'), [
            'name' => 'Budi Santoso',
            'phone' => '08123456789',
            'address' => 'Jl. Mawar No. 10, Bandung',
        ])->assertRedirect();

        $customer = $customer->fresh();
        $this->assertEquals('Budi Santoso', $customer->name);
        $this->assertEquals('08123456789', $customer->phone);
        $this->assertEquals('Jl. Mawar No. 10, Bandung', $customer->address);

        $this->put(route('profile.update'), [
            'name' => 'Budi',
            'phone' => 'bukan-nomor',
        ])->assertSessionHasErrors('phone');
    }

    public function test_checkout_prefills_and_saves_profile(): void
    {
        $this->seed();

        $customer = User::where('email', 'customer@shopedia.test')->first();
        $customer->update(['phone' => '08111111111', 'address' => 'Jl. Profil No. 1']);
        $product = Product::where('stock', '>', 5)->first();
        $method = \App\Models\PaymentMethod::where('slug', 'bca')->first();

        $this->actingAs($customer);

        // Prefill dari profil
        $this->post(route('cart.store', $product), ['quantity' => 1])->assertRedirect();
        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('08111111111', false)
            ->assertSee('Jl. Profil No. 1', false);

        // Checkout tanpa centang: profil tidak berubah
        $this->post(route('checkout.store'), [
            'recipient_name' => 'Orang Lain',
            'recipient_phone' => '08222222222',
            'shipping_address' => 'Alamat lain yang panjang',
            'courier' => 'jne',
            'shipping_service' => 'reguler',
            'payment_method_id' => $method->id,
        ])->assertRedirect();

        $this->assertEquals('08111111111', $customer->fresh()->phone);

        // Checkout dengan centang: profil diperbarui
        $this->post(route('cart.store', $product), ['quantity' => 1])->assertRedirect();
        $this->post(route('checkout.store'), [
            'recipient_name' => 'Budi Baru',
            'recipient_phone' => '08333333333',
            'shipping_address' => 'Alamat baru yang panjang',
            'courier' => 'jne',
            'shipping_service' => 'reguler',
            'save_profile' => true,
            'payment_method_id' => $method->id,
        ])->assertRedirect();

        $customer = $customer->fresh();
        $this->assertEquals('Budi Baru', $customer->name);
        $this->assertEquals('08333333333', $customer->phone);
        $this->assertEquals('Alamat baru yang panjang', $customer->address);
    }

    public function test_guest_cannot_access_profile(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }
}
