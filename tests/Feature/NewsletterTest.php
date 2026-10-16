<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_subscribe_and_duplicate_rejected(): void
    {
        $this->post(route('newsletter.store'), ['email' => 'promo@example.com'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('subscribers', ['email' => 'promo@example.com']);

        $this->post(route('newsletter.store'), ['email' => 'promo@example.com'])
            ->assertSessionHasErrors('email');

        $this->post(route('newsletter.store'), ['email' => 'bukan-email'])
            ->assertSessionHasErrors('email');
    }

    public function test_admin_can_view_subscribers(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@shopedia.test')->first();
        $customer = User::where('email', 'customer@shopedia.test')->first();

        $this->actingAs($customer);
        $this->get(route('admin.subscribers.index'))->assertForbidden();

        $this->actingAs($admin);
        $this->post(route('newsletter.store'), ['email' => 'a@example.com'])->assertRedirect();
        $this->get(route('admin.subscribers.index'))->assertOk()->assertSee('a@example.com');
    }

    public function test_info_pages_load(): void
    {
        foreach (['syarat-ketentuan', 'kebijakan-privasi', 'pengembalian-barang'] as $slug) {
            $this->get(route('info.show', $slug))->assertOk();
        }

        $this->get('/info/tidak-ada')->assertNotFound();
    }
}
