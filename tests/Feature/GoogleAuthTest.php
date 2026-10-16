<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;
    public function test_login_page_shows_google_button(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('google.redirect'), false);
    }

    public function test_register_page_shows_google_button(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee(route('google.redirect'), false);
    }
}
