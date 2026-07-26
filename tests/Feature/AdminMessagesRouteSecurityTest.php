<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMessagesRouteSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_admin_messages(): void
    {
        $response = $this->get('/admin/messages');

        $response->assertRedirect('/my-account');
    }

    public function test_regular_user_cannot_view_admin_messages(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/messages');

        $response->assertRedirect('/my-account');
    }
}
