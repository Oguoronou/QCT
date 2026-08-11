<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use Database\Seeders\CommissariatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissariatSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_active_commissariats_for_abidjan_communes(): void
    {
        $this->seed(CommissariatSeeder::class);

        $this->assertGreaterThanOrEqual(8, Commissariat::where('is_active', true)->count());
        $this->assertDatabaseHas('commissariats', ['commune' => 'Cocody', 'city' => 'Abidjan']);
    }
}
