<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTestimonialToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_mark_a_message_as_a_testimonial(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $message = Message::create([
            'name' => 'Jean Koffi',
            'email' => 'jean@example.com',
            'message' => 'Merci QCT !',
        ]);

        $response = $this->actingAs($admin)->post('/admin/toggle-testimonial/' . $message->id);

        $response->assertRedirect();
        $this->assertTrue($message->fresh()->is_testimonial);
    }

    public function test_admin_can_unmark_a_testimonial(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $message = Message::create([
            'name' => 'Jean Koffi',
            'email' => 'jean@example.com',
            'message' => 'Merci QCT !',
            'is_testimonial' => true,
        ]);

        $response = $this->actingAs($admin)->post('/admin/toggle-testimonial/' . $message->id);

        $response->assertRedirect();
        $this->assertFalse($message->fresh()->is_testimonial);
    }

    public function test_non_admin_cannot_toggle_testimonial(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $message = Message::create([
            'name' => 'Jean Koffi',
            'email' => 'jean@example.com',
            'message' => 'Merci QCT !',
        ]);

        $response = $this->actingAs($user)->post('/admin/toggle-testimonial/' . $message->id);

        $response->assertRedirect('/my-account');
        $this->assertFalse($message->fresh()->is_testimonial);
    }
}
