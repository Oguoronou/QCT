<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Contenu de départ générique, à adapter par un administrateur
     * depuis l'espace Admin > Contenu.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'comment-ca-marche',
                'title' => 'Comment ça marche',
                'content' => <<<'HTML'
<h2>Comment fonctionne QCT ?</h2>
<p>QCT (&laquo; Qui Cherche, Trouve &raquo;) est une plateforme communautaire qui connecte les personnes ayant perdu un objet avec celles qui l'ont retrouv&eacute;, partout en C&ocirc;te d'Ivoire.</p>
<h3>1. Signalez</h3>
<p>Cr&eacute;ez une annonce en quelques minutes&nbsp;: d&eacute;crivez l'objet perdu ou trouv&eacute;, ajoutez des photos et pr&eacute;cisez le lieu et la date.</p>
<h3>2. La communaut&eacute; agit</h3>
<p>Votre annonce est visible par l'ensemble des membres de la plateforme, qui peuvent vous transmettre des informations utiles.</p>
<h3>3. Mise en relation s&eacute;curis&eacute;e</h3>
<p>Lorsqu'une correspondance est trouv&eacute;e, les deux parties sont mises en relation pour organiser la restitution de l'objet en toute confiance.</p>
<h3>4. Retrouvailles</h3>
<p>L'objet retrouve son propri&eacute;taire, ou la personne disparue est signal&eacute;e aux autorit&eacute;s comp&eacute;tentes. Pour les objets trouv&eacute;s, une d&eacute;claration au commissariat le plus proche peut &ecirc;tre demand&eacute;e avant la remise, conform&eacute;ment &agrave; la r&eacute;glementation en vigueur en C&ocirc;te d'Ivoire.</p>
HTML,
            ],
            [
                'slug' => 'politique-confidentialite',
                'title' => 'Politique de confidentialité',
                'content' => <<<'HTML'
<h2>Politique de confidentialit&eacute;</h2>
<p>QCT accorde une grande importance &agrave; la protection de vos donn&eacute;es personnelles. Cette politique explique quelles informations nous collectons et comment elles sont utilis&eacute;es.</p>
<h3>Donn&eacute;es collect&eacute;es</h3>
<p>Nous collectons les informations que vous nous fournissez lors de votre inscription (nom, email) et lors de la publication d'une annonce (description, photos, localisation approximative).</p>
<h3>Utilisation des donn&eacute;es</h3>
<p>Vos donn&eacute;es sont utilis&eacute;es uniquement pour permettre le bon fonctionnement de la plateforme&nbsp;: mise en relation entre utilisateurs, notifications li&eacute;es &agrave; vos annonces, et am&eacute;lioration du service.</p>
<h3>Partage des donn&eacute;es</h3>
<p>Vos donn&eacute;es ne sont jamais vendues &agrave; des tiers. Elles peuvent &ecirc;tre partag&eacute;es avec les autorit&eacute;s comp&eacute;tentes (commissariats) dans le cadre l&eacute;gal de la restitution d'objets trouv&eacute;s.</p>
<h3>Vos droits</h3>
<p>Vous pouvez &agrave; tout moment demander l'acc&egrave;s, la rectification ou la suppression de vos donn&eacute;es personnelles en nous contactant via la page Contact.</p>
HTML,
            ],
            [
                'slug' => 'cgu',
                'title' => "Conditions Générales d'Utilisation",
                'content' => <<<'HTML'
<h2>Conditions G&eacute;n&eacute;rales d'Utilisation</h2>
<p>L'utilisation de la plateforme QCT implique l'acceptation pleine et enti&egrave;re des pr&eacute;sentes conditions g&eacute;n&eacute;rales d'utilisation.</p>
<h3>Objet</h3>
<p>QCT met &agrave; disposition une plateforme communautaire permettant de signaler et retrouver des objets perdus ou trouv&eacute;s, ainsi que des personnes disparues, en C&ocirc;te d'Ivoire.</p>
<h3>Inscription</h3>
<p>L'inscription est gratuite et requiert la fourniture d'informations exactes. Chaque utilisateur est responsable de la confidentialit&eacute; de ses identifiants de connexion.</p>
<h3>Obligations de l'utilisateur</h3>
<p>L'utilisateur s'engage &agrave; publier des annonces exactes et de bonne foi, &agrave; ne pas usurper l'identit&eacute; d'un tiers, et &agrave; respecter les lois en vigueur en C&ocirc;te d'Ivoire, notamment concernant la d&eacute;claration des objets trouv&eacute;s aupr&egrave;s des autorit&eacute;s.</p>
<h3>Responsabilit&eacute;</h3>
<p>QCT agit en tant qu'interm&eacute;diaire entre utilisateurs et ne garantit pas la restitution effective d'un objet ni l'exactitude des informations publi&eacute;es par les utilisateurs.</p>
HTML,
            ],
            [
                'slug' => 'cgv',
                'title' => 'Conditions Générales de Vente',
                'content' => <<<'HTML'
<h2>Conditions G&eacute;n&eacute;rales de Vente</h2>
<p>La plateforme QCT est gratuite pour la publication et la consultation d'annonces d'objets perdus ou trouv&eacute;s.</p>
<h3>Dons</h3>
<p>QCT propose une fonctionnalit&eacute; de don volontaire destin&eacute;e &agrave; soutenir le fonctionnement et le d&eacute;veloppement de la plateforme. Les dons sont trait&eacute;s par un prestataire de paiement tiers s&eacute;curis&eacute;.</p>
<h3>Aucune contrepartie commerciale</h3>
<p>Un don effectu&eacute; sur QCT ne constitue pas un paiement pour un service et n'ouvre droit &agrave; aucune contrepartie commerciale. Il s'agit d'un soutien volontaire &agrave; la mission de la plateforme.</p>
<h3>Remboursement</h3>
<p>Les dons effectu&eacute;s via QCT ne sont pas remboursables, sauf erreur manifeste de transaction signal&eacute;e dans les 48 heures suivant le paiement, &agrave; adresser via la page Contact.</p>
HTML,
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(['slug' => $page['slug']], [
                'title' => $page['title'],
                'content' => $page['content'],
            ]);
        }
    }
}
