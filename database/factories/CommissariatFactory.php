<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commissariat>
 */
class CommissariatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Commissariat de ' . fake()->city(),
            'commune' => fake()->city(),
            'city' => 'Abidjan',
            'phone' => null,
            'address' => null,
            'is_active' => true,
        ];
    }
}
