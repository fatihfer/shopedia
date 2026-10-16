<?php

namespace Tests\Feature;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterOtpTest extends TestCase
{
    use RefreshDatabase;

    private function register(array $overrides = []): array
    {
        Mail::fake();

        $this->post(route('register'), array_merge([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides))->assertRedirect(route('register.verify'));

        $code = null;
        Mail::assertSent(OtpMail::class, function ($mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $this->assertNotNull($code);

        return [$code];
    }

    public function test_register_sends_otp_and_verify_creates_user(): void
    {
        [$code] = $this->register();

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'budi@example.com']);

        $this->get(route('register.verify'))->assertOk()->assertSee('budi@example.com');

        $this->post(route('register.verify.store'), ['code' => $code])
            ->assertRedirect(route('products.index'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'budi@example.com', 'role' => 'customer']);
    }

    public function test_wrong_code_is_rejected(): void
    {
        $this->register();

        $this->post(route('register.verify.store'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'budi@example.com']);
    }

    public function test_verify_without_pending_redirects_to_register(): void
    {
        $this->get(route('register.verify'))->assertRedirect(route('register'));
        $this->post(route('register.verify.store'), ['code' => '123456'])
            ->assertRedirect(route('register'));
    }

    public function test_resend_is_throttled_then_allowed(): void
    {
        $this->register();

        // Langsung kirim ulang: ditolak (jeda 60 detik)
        $this->post(route('register.resend'))->assertSessionHas('error');

        // Majukan waktu 61 detik
        $this->travel(61)->seconds();
        $this->post(route('register.resend'))->assertSessionHas('success');

        $this->assertGuest();
    }
}
