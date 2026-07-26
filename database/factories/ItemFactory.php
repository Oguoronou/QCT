<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'found_user_id' => null,
            'item_name' => fake()->words(2, true),
            'category_name' => 'objets',
            'date' => fake()->date(),
            'images' => null,
            'description' => fake()->sentence(),
            'status' => 'lost',
            'lost_found_status' => 'pending',
        ];
    }
}
