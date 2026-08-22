<?php

namespace Tests\Feature;

use App\Models\FaqItem;
use Database\Seeders\FaqItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqItemSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_six_faq_items(): void
    {
        (new FaqItemSeeder())->run();

        $this->assertSame(6, FaqItem::count());
    }

    public function test_seeder_does_not_duplicate_or_overwrite_edited_answers(): void
    {
        (new FaqItemSeeder())->run();
        $item = FaqItem::where('question', "L'utilisation de QCT est-elle payante ?")->firstOrFail();
        $item->update(['answer' => 'Réponse modifiée par un admin']);

        (new FaqItemSeeder())->run();

        $this->assertSame(6, FaqItem::count());
        $this->assertSame('Réponse modifiée par un admin', $item->fresh()->answer);
    }
}
