<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelFactoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_factory_creates_a_persistable_user(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_item_factory_creates_a_persistable_item(): void
    {
        $item = Item::factory()->create();

        $this->assertDatabaseHas('items', ['id' => $item->id]);
    }
}
