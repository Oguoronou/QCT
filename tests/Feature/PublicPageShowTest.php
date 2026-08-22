<?php

namespace Tests\Feature;

use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new PageSeeder())->run();
    }

    public function test_existing_page_slug_returns_200_with_its_content(): void
    {
        $response = $this->get('/page/cgu');

        $response->assertStatus(200);
        $response->assertSee("Conditions Générales d'Utilisation");
    }

    public function test_unknown_slug_returns_404(): void
    {
        $response = $this->get('/page/does-not-exist');

        $response->assertStatus(404);
    }
}
