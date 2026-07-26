<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_detail_page_shows_owner_information(): void
    {
        $viewer = User::factory()->create();
        $owner = User::factory()->create(['name' => 'Fatou Bamba']);
        $item = Item::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($viewer)->get('/item-detail/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee('Fatou Bamba');
    }
}
