<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\ItemPoliceDeclaration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemPoliceDeclarationRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_has_one_police_declaration_with_commissariat(): void
    {
        $item = Item::factory()->create(['status' => 'found']);
        $commissariat = Commissariat::factory()->create(['name' => 'Commissariat de Cocody']);
        $declarant = User::factory()->create();

        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $declarant->id,
            'declaration_number' => 'DEC-2026-001',
        ]);

        $item->refresh();

        $this->assertNotNull($item->policeDeclaration);
        $this->assertEquals('Commissariat de Cocody', $item->policeDeclaration->commissariat->name);
        $this->assertEquals('DEC-2026-001', $item->policeDeclaration->declaration_number);
        $this->assertEquals($declarant->id, $item->policeDeclaration->declaredBy->id);
    }
}
