<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_item_detail_with_owner_information(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['name' => 'Awa Koné']);
        $item = Item::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($admin)->get('/admin/item-detail/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('Awa Koné');
    }
}
