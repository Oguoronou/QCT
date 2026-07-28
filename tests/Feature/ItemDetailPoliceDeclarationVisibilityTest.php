<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\ItemPoliceDeclaration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailPoliceDeclarationVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_finder_sees_the_declaration_form_when_nothing_declared_yet(): void
    {
        $finder = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'pending',
        ]);

        $response = $this->actingAs($finder)->get('/item-detail/' . $item->id);

        $response->assertSee('Marquer comme déposé');
    }

    public function test_third_party_sees_commissariat_name_but_not_declaration_number(): void
    {
        $finder = User::factory()->create();
        $stranger = User::factory()->create();
        $commissariat = Commissariat::factory()->create(['name' => 'Commissariat de Marcory', 'commune' => 'Marcory']);
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'found',
        ]);
        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-SECRET-1',
        ]);

        $response = $this->actingAs($stranger)->get('/item-detail/' . $item->id);

        $response->assertSee('Commissariat de Marcory');
        $response->assertDontSee('DEC-SECRET-1');
    }

    public function test_finder_sees_the_declaration_number(): void
    {
        $finder = User::factory()->create();
        $commissariat = Commissariat::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'found',
        ]);
        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-VISIBLE-1',
        ]);

        $response = $this->actingAs($finder)->get('/item-detail/' . $item->id);

        $response->assertSee('DEC-VISIBLE-1');
    }

    public function test_claimant_sees_declaration_number_only_after_ownership_is_validated(): void
    {
        $finder = User::factory()->create();
        $claimant = User::factory()->create();
        $commissariat = Commissariat::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'found_user_id' => $claimant->id,
            'status' => 'found',
            'lost_found_status' => 'ownership_claimed',
        ]);
        ItemPoliceDeclaration::factory()->create([
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-PENDING-1',
        ]);

        $beforeValidation = $this->actingAs($claimant)->get('/item-detail/' . $item->id);
        $beforeValidation->assertDontSee('DEC-PENDING-1');

        $item->update(['lost_found_status' => 'returned']);

        $afterValidation = $this->actingAs($claimant)->get('/item-detail/' . $item->id);
        $afterValidation->assertSee('DEC-PENDING-1');
    }
}
