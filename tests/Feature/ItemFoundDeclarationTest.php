<?php

namespace Tests\Feature;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ItemFoundDeclarationTest extends TestCase
{
    use RefreshDatabase;

    public function test_marking_a_found_item_as_found_requires_a_police_declaration(): void
    {
        $finder = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'pending',
        ]);

        $response = $this->actingAs($finder)->post('/item-found/' . $item->id, []);

        $response->assertRedirect();
        $this->assertEquals('pending', $item->fresh()->lost_found_status);
        $this->assertDatabaseMissing('item_police_declarations', ['item_id' => $item->id]);
    }

    public function test_marking_a_found_item_as_found_creates_the_police_declaration(): void
    {
        $finder = User::factory()->create();
        $commissariat = Commissariat::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'pending',
        ]);

        $response = $this->actingAs($finder)->post('/item-found/' . $item->id, [
            'commissariat_id' => $commissariat->id,
            'declaration_number' => 'DEC-2026-042',
        ]);

        $response->assertRedirect('my-items');
        $this->assertEquals('found', $item->fresh()->lost_found_status);
        $this->assertDatabaseHas('item_police_declarations', [
            'item_id' => $item->id,
            'commissariat_id' => $commissariat->id,
            'declared_by_user_id' => $finder->id,
            'declaration_number' => 'DEC-2026-042',
        ]);
    }

    public function test_marking_a_found_item_stores_the_optional_receipt_photo(): void
    {
        $finder = User::factory()->create();
        $commissariat = Commissariat::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'status' => 'found',
            'lost_found_status' => 'pending',
        ]);
        $photo = UploadedFile::fake()->image('recepisse.jpg');

        $response = $this->actingAs($finder)->post('/item-found/' . $item->id, [
            'commissariat_id' => $commissariat->id,
            'declaration_number' => 'DEC-2026-099',
            'receipt_photo' => $photo,
        ]);

        $response->assertRedirect('my-items');
        $declaration = $item->fresh()->policeDeclaration;
        $this->assertNotNull($declaration->receipt_photo);
        $this->assertFileExists(public_path($declaration->receipt_photo));

        unlink(public_path($declaration->receipt_photo));
    }

    public function test_marking_a_lost_item_as_found_does_not_require_a_declaration(): void
    {
        $owner = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $owner->id,
            'status' => 'lost',
            'lost_found_status' => 'pending',
        ]);

        $response = $this->actingAs($owner)->post('/item-found/' . $item->id, []);

        $response->assertRedirect('my-items');
        $this->assertEquals('found', $item->fresh()->lost_found_status);
        $this->assertDatabaseMissing('item_police_declarations', ['item_id' => $item->id]);
    }
}
