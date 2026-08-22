<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_the_four_expected_pages(): void
    {
        (new PageSeeder())->run();

        $slugs = Page::pluck('slug')->sort()->values()->all();

        $this->assertSame(
            ['cgu', 'cgv', 'comment-ca-marche', 'politique-confidentialite'],
            $slugs
        );
    }

    public function test_seeder_does_not_overwrite_an_already_edited_page(): void
    {
        (new PageSeeder())->run();
        $page = Page::where('slug', 'cgu')->firstOrFail();
        $page->update(['content' => 'Contenu personnalisé par un admin']);

        (new PageSeeder())->run();

        $this->assertSame('Contenu personnalisé par un admin', $page->fresh()->content);
    }
}
