<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\ItemPoliceDeclaration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemPoliceDeclarationEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_finder_can_correct_an_existing_declaration(): void
    {
        $finder = User::factory()->create();
        $originalCommissariat = Commissariat::factory()->create();
        $correctedCommissariat = Commissariat::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'found',
            'item_name' => 'Clés de voiture',
            'category_name' => 'objets',
            'date' => '2026-07-01',
            'description' => 'Trouvées au marché',
        ]);
        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $originalCommissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-OLD-1',
        ]);

        $response = $this->actingAs($finder)->post('/update-item', [
            'id' => $item->id,
            'item_name' => $item->item_name,
            'category' => $item->category_name,
            'lost_date' => $item->date,
            'description' => $item->description,
            'commissariat_id' => $correctedCommissariat->id,
            'declaration_number' => 'DEC-NEW-1',
        ]);

        $response->assertRedirect('my-items');
        $this->assertDatabaseHas('item_police_declarations', [
            'item_id' => $item->id,
            'commissariat_id' => $correctedCommissariat->id,
            'declaration_number' => 'DEC-NEW-1',
        ]);
    }
}
