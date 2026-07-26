<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardCounterTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivered_items_counter_reflects_delivered_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Item::factory()->create(['lost_found_status' => 'delivered']);
        Item::factory()->create(['lost_found_status' => 'delivered']);
        Item::factory()->create(['lost_found_status' => 'pending']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertViewHas('deliverItems', 2);
    }
}
