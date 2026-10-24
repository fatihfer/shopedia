<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InfoPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_info_pages_load(): void
    {
        foreach (['syarat-ketentuan', 'kebijakan-privasi', 'pengembalian-barang'] as $slug) {
            $this->get(route('info.show', $slug))->assertOk();
        }

        $this->get('/info/tidak-ada')->assertNotFound();
    }
}
