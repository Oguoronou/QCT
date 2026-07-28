<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Personnes', 'icon' => 'fa fa-user'],
            ['category_name' => 'Portefeuilles', 'icon' => 'fa fa-wallet'],
            ['category_name' => 'Téléphones', 'icon' => 'fas fa-mobile-alt'],
            ['category_name' => 'Clés', 'icon' => 'fas fa-key'],
            ['category_name' => 'Documents', 'icon' => 'fas fa-id-card'],
            ['category_name' => 'Animaux', 'icon' => 'fas fa-paw'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['category_name' => $category['category_name']], $category);
        }
    }
}
