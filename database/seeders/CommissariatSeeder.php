<?php

namespace Database\Seeders;

use App\Models\Commissariat;
use Illuminate\Database\Seeder;

class CommissariatSeeder extends Seeder
{
    /**
     * Noms et communes uniquement : téléphone et adresse doivent être
     * vérifiés et complétés par un administrateur avant d'être
     * communiqués aux utilisateurs — on n'invente pas de coordonnées
     * institutionnelles non vérifiées.
     */
    public function run(): void
    {
        $commissariats = [
            ['name' => 'Commissariat du Plateau', 'commune' => 'Le Plateau'],
            ['name' => 'Commissariat de Cocody', 'commune' => 'Cocody'],
            ['name' => 'Commissariat de Yopougon', 'commune' => 'Yopougon'],
            ['name' => "Commissariat d'Adjamé", 'commune' => 'Adjamé'],
            ['name' => 'Commissariat de Treichville', 'commune' => 'Treichville'],
            ['name' => 'Commissariat de Marcory', 'commune' => 'Marcory'],
            ['name' => 'Commissariat de Koumassi', 'commune' => 'Koumassi'],
            ['name' => "Commissariat d'Abobo", 'commune' => 'Abobo'],
        ];

        foreach ($commissariats as $commissariat) {
            Commissariat::firstOrCreate(
                ['name' => $commissariat['name'], 'commune' => $commissariat['commune']],
                ['city' => 'Abidjan', 'is_active' => true]
            );
        }
    }
}
