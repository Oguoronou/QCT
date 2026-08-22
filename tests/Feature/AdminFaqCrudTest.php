<?php

namespace Tests\Feature;

use App\Models\FaqItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFaqCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_faq_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/faq');

        $response->assertRedirect('/my-account');
    }

    public function test_admin_can_create_a_faq_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/save-faq', [
            'question' => 'Ma question de test ?',
            'answer' => 'Ma réponse de test.',
            'order' => 3,
        ]);

        $response->assertRedirect('admin/faq');
        $this->assertDatabaseHas('faq_items', ['question' => 'Ma question de test ?', 'order' => 3]);
    }

    public function test_admin_can_update_a_faq_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $faqItem = FaqItem::create(['question' => 'Q ?', 'answer' => 'A.', 'order' => 0]);

        $response = $this->actingAs($admin)->post('/admin/update-faq/' . $faqItem->id, [
            'question' => 'Q modifiée ?',
            'answer' => 'A modifiée.',
            'order' => 1,
        ]);

        $response->assertRedirect('admin/faq');
        $this->assertSame('Q modifiée ?', $faqItem->fresh()->question);
    }

    public function test_admin_can_delete_a_faq_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $faqItem = FaqItem::create(['question' => 'Q ?', 'answer' => 'A.', 'order' => 0]);

        $response = $this->actingAs($admin)->post('/admin/delete-faq/' . $faqItem->id);

        $response->assertRedirect('admin/faq');
        $this->assertDatabaseMissing('faq_items', ['id' => $faqItem->id]);
    }
}
