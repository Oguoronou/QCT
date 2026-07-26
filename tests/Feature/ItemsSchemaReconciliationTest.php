<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemsSchemaReconciliationTest extends TestCase
{
    use RefreshDatabase;

    public function test_extended_lost_found_statuses_can_be_persisted(): void
    {
        $item = Item::factory()->create(['lost_found_status' => 'ownership_claimed']);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'lost_found_status' => 'ownership_claimed',
        ]);
    }
}
