<?php

namespace Database\Seeders;

use App\Models\FaqItem;
use Illuminate\Database\Seeder;

class FaqItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'question' => 'Comment signaler un objet perdu ou trouvé ?',
                'answer' => "Connectez-vous à votre compte, cliquez sur « Signaler une perte » ou « Signaler une trouvaille », puis remplissez le formulaire avec une description précise et des photos si possible.",
            ],
            [
                'question' => "L'utilisation de QCT est-elle payante ?",
                'answer' => 'Non, QCT est entièrement gratuit pour publier et consulter des annonces. Un don volontaire est possible pour soutenir la plateforme.',
            ],
            [
                'question' => "Que faire si je retrouve un objet appartenant à quelqu'un d'autre ?",
                'answer' => "Publiez une annonce « objet trouvé » avec un maximum de détails. Selon la réglementation, une déclaration au commissariat le plus proche peut être nécessaire avant la remise de l'objet.",
            ],
            [
                'question' => "Comment récupérer un objet qui m'appartient ?",
                'answer' => "Recherchez votre objet dans la liste des annonces, puis utilisez le bouton « Cet objet m'appartient » sur l'annonce correspondante pour envoyer une demande au déclarant.",
            ],
            [
                'question' => 'Mes données personnelles sont-elles protégées ?',
                'answer' => 'Oui, consultez notre page Politique de confidentialité pour savoir comment vos données sont collectées et utilisées.',
            ],
            [
                'question' => "Comment signaler la disparition d'une personne ?",
                'answer' => "Utilisez la catégorie « Personnes » lors de la création d'une annonce. Ces signalements apparaissent en priorité dans la section « Personnes disparues » de la page d'accueil.",
            ],
        ];

        foreach ($items as $index => $item) {
            FaqItem::firstOrCreate(
                ['question' => $item['question']],
                ['answer' => $item['answer'], 'order' => $index]
            );
        }
    }
}
