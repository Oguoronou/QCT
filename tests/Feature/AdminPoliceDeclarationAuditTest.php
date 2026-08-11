<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\ItemPoliceDeclaration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPoliceDeclarationAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_the_full_declaration_on_item_detail(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $finder = User::factory()->create();
        $commissariat = Commissariat::factory()->create(['name' => "Commissariat d'Abobo"]);
        $item = Item::factory()->create(['user_id' => $finder->id, 'status' => 'found']);
        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-AUDIT-1',
        ]);

        $response = $this->actingAs($admin)->get('/admin/item-detail/' . $item->id);

        $response->assertSee("Commissariat d'Abobo");
        $response->assertSee('DEC-AUDIT-1');
    }
}
