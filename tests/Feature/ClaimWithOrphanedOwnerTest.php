<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimWithOrphanedOwnerTest extends TestCase
{
    use RefreshDatabase;

    public function test_claiming_a_lost_item_whose_owner_was_deleted_does_not_crash(): void
    {
        $owner = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $owner->id,
            'status' => 'lost',
            'lost_found_status' => 'pending',
        ]);
        $owner->delete();

        $finder = User::factory()->create();

        $response = $this->actingAs($finder)->post('/claim-item/' . $item->id);

        $response->assertRedirect();
    }

    public function test_claiming_ownership_of_a_found_item_whose_owner_was_deleted_does_not_crash(): void
    {
        $poster = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $poster->id,
            'category_name' => 'Personnes',
            'status' => 'found',
            'lost_found_status' => 'pending',
        ]);
        $poster->delete();

        $claimant = User::factory()->create();

        $response = $this->actingAs($claimant)->post('/claim-ownership/' . $item->id);

        $response->assertRedirect();
    }
}
