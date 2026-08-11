<?php

namespace Database\Factories;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemPoliceDeclaration>
 */
class ItemPoliceDeclarationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'commissariat_id' => Commissariat::factory(),
            'declared_by_user_id' => User::factory(),
            'declaration_number' => strtoupper(fake()->bothify('DEC-####-????')),
            'receipt_photo' => null,
            'declared_at' => now(),
        ];
    }
}
