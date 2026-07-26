<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Notifications\OwnershipValidatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnershipValidationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_validating_ownership_notifies_the_claimant_in_database(): void
    {
        $poster = User::factory()->create();
        $claimant = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $poster->id,
            'found_user_id' => $claimant->id,
            'status' => 'found',
            'lost_found_status' => 'ownership_claimed',
        ]);

        $response = $this->actingAs($poster)->post('/validate-ownership/' . $item->id);

        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $claimant->id,
            'notifiable_type' => User::class,
            'type' => OwnershipValidatedNotification::class,
        ]);
    }
}
