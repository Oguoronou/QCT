<?php

namespace Database\Seeders;

use App\Models\Commissariat;
use App\Models\Item;
use App\Models\ItemPoliceDeclaration;
use App\Models\User;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Couvre chaque état du workflow (lost/found × pending/claimed/
     * ownership_claimed/delivered/returned). Une déclaration au
     * commissariat n'est créée que pour les items status=found dont
     * lost_found_status est déjà passé par itemFound (found,
     * ownership_claimed, returned) — cohérent avec la règle métier.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();

        if ($users->count() < 2) {
            return;
        }

        $commissariats = Commissariat::where('is_active', true)->get();

        $items = [
            // Objets perdus, toujours en recherche
            ['item_name' => 'Portefeuille en cuir marron', 'category_name' => 'Portefeuilles', 'status' => 'lost', 'lost_found_status' => 'pending', 'description' => "Portefeuille en cuir marron perdu près du marché de Cocody, contient des papiers d'identité."],
            ['item_name' => 'iPhone 13 noir', 'category_name' => 'Téléphones', 'status' => 'lost', 'lost_found_status' => 'pending', 'description' => 'Téléphone perdu dans un taxi entre Adjamé et Plateau, coque noire avec une fissure sur le bord.'],
            ['item_name' => 'Trousseau de clés avec porte-clés éléphant', 'category_name' => 'Clés', 'status' => 'lost', 'lost_found_status' => 'pending', 'description' => "Trousseau de 4 clés, porte-clés en bois en forme d'éléphant."],
            ['item_name' => "Chien Berger de Côte d'Ivoire", 'category_name' => 'Animaux', 'status' => 'lost', 'lost_found_status' => 'pending', 'description' => 'Chien répondant au nom de Rex, échappé du domicile à Yopougon Selmer.'],

            // Objets perdus, réclamés par un trouveur (en attente de validation du propriétaire)
            ['item_name' => 'Carte d\'identité et permis de conduire', 'category_name' => 'Documents', 'status' => 'lost', 'lost_found_status' => 'claimed', 'description' => 'Pochette de documents administratifs perdue au marché de Treichville.', 'with_finder' => true],
            ['item_name' => 'Sac à main beige', 'category_name' => 'Portefeuilles', 'status' => 'lost', 'lost_found_status' => 'claimed', 'description' => 'Sac à main perdu à la gare de Bouaké.', 'with_finder' => true],

            // Objet perdu, restitué (workflow complet)
            ['item_name' => 'Montre connectée', 'category_name' => 'Téléphones', 'status' => 'lost', 'lost_found_status' => 'delivered', 'description' => 'Montre connectée perdue puis restituée par son trouveur.', 'with_finder' => true],

            // Objets trouvés, pas encore signalés au commissariat
            ['item_name' => 'Téléphone Samsung trouvé', 'category_name' => 'Téléphones', 'status' => 'found', 'lost_found_status' => 'pending', 'description' => 'Téléphone trouvé sur un banc à Marcory Résidentiel.'],
            ['item_name' => 'Clés de moto trouvées', 'category_name' => 'Clés', 'status' => 'found', 'lost_found_status' => 'pending', 'description' => 'Clés de moto trouvées devant une pharmacie à Yamoussoukro.'],
            ['item_name' => 'Portefeuille trouvé au marché', 'category_name' => 'Portefeuilles', 'status' => 'found', 'lost_found_status' => 'pending', 'description' => "Portefeuille trouvé au marché d'Adjamé, contient de l'argent et des papiers."],
            ['item_name' => 'Enfant retrouvé errant', 'category_name' => 'Personnes', 'status' => 'found', 'lost_found_status' => 'pending', 'description' => 'Jeune enfant retrouvé seul près du grand marché, semble avoir 5-6 ans.'],

            // Objets trouvés, déjà déposés au commissariat
            ['item_name' => 'Passeport trouvé', 'category_name' => 'Documents', 'status' => 'found', 'lost_found_status' => 'found', 'description' => 'Passeport trouvé et déposé au commissariat le plus proche.', 'with_declaration' => true],
            ['item_name' => 'Chat trouvé errant', 'category_name' => 'Animaux', 'status' => 'found', 'lost_found_status' => 'found', 'description' => 'Chat trouvé errant à Cocody, propre et sociable.', 'with_declaration' => true],

            // Objet trouvé, réclamé par son propriétaire (en attente de validation)
            ['item_name' => 'Sac à dos trouvé', 'category_name' => 'Portefeuilles', 'status' => 'found', 'lost_found_status' => 'ownership_claimed', 'description' => 'Sac à dos trouvé et réclamé par un propriétaire potentiel.', 'with_finder' => true, 'with_declaration' => true],

            // Objet trouvé, restitué (workflow complet)
            ['item_name' => 'Clés de voiture rendues', 'category_name' => 'Clés', 'status' => 'found', 'lost_found_status' => 'returned', 'description' => 'Clés de voiture trouvées puis rendues à leur propriétaire.', 'with_finder' => true, 'with_declaration' => true],
        ];

        foreach ($items as $index => $data) {
            $poster = $users[$index % $users->count()];
            $finder = $users[($index + 1) % $users->count()];

            $exists = Item::where('user_id', $poster->id)
                ->where('item_name', $data['item_name'])
                ->exists();

            if ($exists) {
                continue;
            }

            $item = Item::create([
                'user_id' => $poster->id,
                'found_user_id' => ($data['with_finder'] ?? false) ? $finder->id : null,
                'item_name' => $data['item_name'],
                'category_name' => $data['category_name'],
                'date' => now()->subDays(random_int(1, 30))->toDateString(),
                'images' => null,
                'description' => $data['description'],
                'status' => $data['status'],
                'lost_found_status' => $data['lost_found_status'],
            ]);

            if (($data['with_declaration'] ?? false) && $commissariats->isNotEmpty()) {
                ItemPoliceDeclaration::create([
                    'item_id' => $item->id,
                    'commissariat_id' => $commissariats->random()->id,
                    'declared_by_user_id' => $item->user_id,
                    'declaration_number' => 'DEC-' . now()->format('Y') . '-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'declared_at' => now()->subDays(random_int(1, 10)),
                ]);
            }
        }
    }
}
