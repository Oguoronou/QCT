<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLostFoundListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_lost_and_found_list_with_owner_names(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['name' => 'Kouassi Yao']);
        Item::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($admin)->get('/admin/lost-and-found');

        $response->assertStatus(200);
        $response->assertSee('Kouassi Yao');
    }
}
