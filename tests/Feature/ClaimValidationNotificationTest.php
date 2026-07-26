<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Notifications\ClaimValidatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimValidationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_validating_a_claim_notifies_the_finder_in_database(): void
    {
        $owner = User::factory()->create();
        $finder = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $owner->id,
            'found_user_id' => $finder->id,
            'lost_found_status' => 'claimed',
        ]);

        $response = $this->actingAs($owner)->post('/validate-claim/' . $item->id);

        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $finder->id,
            'notifiable_type' => User::class,
            'type' => ClaimValidatedNotification::class,
        ]);
    }
}
