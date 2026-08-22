<?php

namespace Tests\Feature;

use Database\Seeders\FaqItemSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFaqPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_faq_page_lists_seeded_questions_in_order(): void
    {
        (new FaqItemSeeder())->run();

        $response = $this->get('/faq');

        $response->assertStatus(200);
        $response->assertSee('Comment signaler un objet perdu ou trouvé ?');
    }

    public function test_faq_page_renders_without_error_when_empty(): void
    {
        $response = $this->get('/faq');

        $response->assertStatus(200);
        $response->assertSee('Aucune question pour le moment.');
    }

    public function test_homepage_footer_links_to_faq_and_content_pages(): void
    {
        (new PageSeeder())->run();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(url('/faq'), false);
        $response->assertSee(url('/page/cgu'), false);
        $response->assertSee(url('/page/cgv'), false);
    }
}
