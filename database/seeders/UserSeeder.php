<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Mot de passe de tous les comptes de démonstration : "password"
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Aya Kouassi', 'email' => 'aya.kouassi@example.com', 'mobile_no' => '0708112233', 'city' => 'Abidjan', 'address' => 'Cocody Angré'],
            ['name' => 'Yao Brou', 'email' => 'yao.brou@example.com', 'mobile_no' => '0709223344', 'city' => 'Abidjan', 'address' => 'Yopougon Selmer'],
            ['name' => 'Fatoumata Diabaté', 'email' => 'fatoumata.diabate@example.com', 'mobile_no' => '0505334455', 'city' => 'Bouaké', 'address' => 'Belleville'],
            ['name' => "Kouadio N'Guessan", 'email' => 'kouadio.nguessan@example.com', 'mobile_no' => '0102445566', 'city' => 'Abidjan', 'address' => 'Marcory Résidentiel'],
            ['name' => 'Adjoua Tanoh', 'email' => 'adjoua.tanoh@example.com', 'mobile_no' => '0759556677', 'city' => 'Yamoussoukro', 'address' => 'Habitat'],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'mobile_no' => $user['mobile_no'],
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'country' => "Côte d'Ivoire",
                    'city' => $user['city'],
                    'address' => $user['address'],
                    'status' => 'active',
                    'role' => 'user',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
