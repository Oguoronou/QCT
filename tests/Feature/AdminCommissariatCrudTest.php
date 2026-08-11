<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommissariatCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_commissariat(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/save-commissariat', [
            'name' => 'Commissariat de Yopougon',
            'commune' => 'Yopougon',
            'city' => 'Abidjan',
        ]);

        $response->assertRedirect('admin/commissariats');
        $this->assertDatabaseHas('commissariats', ['name' => 'Commissariat de Yopougon', 'is_active' => true]);
    }

    public function test_admin_can_toggle_a_commissariat_active_state(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $commissariat = Commissariat::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->post('/admin/toggle-commissariat/' . $commissariat->id);

        $response->assertRedirect('admin/commissariats');
        $this->assertFalse($commissariat->fresh()->is_active);
    }

    public function test_non_admin_cannot_access_commissariat_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/commissariats');

        $response->assertRedirect('/my-account');
    }
}
