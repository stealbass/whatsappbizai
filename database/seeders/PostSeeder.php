<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Comment automatiser vos factures via WhatsApp',
                'slug' => 'automatiser-factures-whatsapp',
                'excerpt' => 'Découvrez comment envoyer vos factures automatiquement à vos clients directement sur WhatsApp grâce à l\'intelligence artificielle.',
                'content' => '<h2>Pourquoi automatiser vos factures ?</h2>
<p>Envoyer des factures manuellement prend du temps et expose à des erreurs. Avec WhatsAppBizAI, votre assistant IA génère et envoie les factures automatiquement quand vos clients vous contactent.</p>

<h2>Étape 1 : Configurez vos services</h2>
<p>Renseignez vos prestations, tarifs et délais dans l\'onglet <strong>Services</strong> de votre tableau de bord. L\'IA utilise ces informations pour créer des devis et factures instantanément.</p>

<h2>Étape 2 : Activez l\'envoi WhatsApp</h2>
<p>Dans <strong>Paramètres > WhatsApp</strong>, connectez votre compte WhatsApp Business. L\'assistant IA enverra les factures PDF directement dans la conversation.</p>

<h2>Étape 3 : Suivez vos paiements</h2>
<p>Chaque facture envoyée est automatiquement trackée. Vous savez instantanément si elle a été lue, acceptée ou payée.</p>

<blockquote>L\'automatisation de vos factures vous fait gagner en moyenne 5 heures par semaine.</blockquote>

<h2>Les avantages</h2>
<ul>
<li>Envoi instantané via WhatsApp</li>
<li>PDF professionnel généré automatiquement</li>
<li>Suivi des statuts en temps réel</li>
<li>Relances automatiques pour les impayés</li>
</ul>',
                'category' => 'tutorial',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'meta_title' => 'Automatiser factures WhatsApp — Tutoriel WhatsAppBizAI',
                'meta_description' => 'Apprenez à automatiser l\'envoi de factures à vos clients via WhatsApp avec l\'assistant IA de WhatsAppBizAI.',
                'sort_order' => 1,
            ],
            [
                'title' => '5 conseils pour mieux gérer vos contacts clients',
                'slug' => '5-conseils-gestion-contacts-clients',
                'excerpt' => 'Un CRM adapté aux PME africaines : comment organiser, segmenter et fidéliser vos contacts sans outil complexe.',
                'content' => '<h2>1. Centralisez vos contacts</h2>
<p>Arrêtez de chercher dans vos conversations WhatsApp. Importez tous vos contacts dans WhatsAppBizAI en un clic grâce à l\'import CSV.</p>

<h2>2. Segmentez votre base</h2>
<p>Utilisez les statuts (client, prospect, fournisseur) pour organiser vos contacts. Cela vous permet d\'envoyer des messages ciblés.</p>

<h2>3. Notez les interactions</h2>
<p>Chaque conversation est automatiquement enregistrée. Vous savez toujours de quoi vous avez discuté avec chaque contact.</p>

<h2>4. Envoyez des relances personnalisées</h2>
<p>Notre outil de relance génère des messages personnalisés grâce à l\'IA, en se basant sur l\'historique de chaque contact.</p>

<h2>5. Suivez l\'engagement</h2>
<p>Sachez quels contacts sont actifs, lesquels ont besoin d\'un suivi, et priorisez votre temps commercialement.</p>

<blockquote>Un contact bien organisé = un client fidèle.</blockquote>',
                'category' => 'astuce',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(7),
                'meta_title' => '5 conseils gestion contacts clients PME — Blog WhatsAppBizAI',
                'meta_description' => 'Découvrez 5 astuces pour mieux gérer vos contacts clients et booster votre fidélisation avec WhatsAppBizAI.',
                'sort_order' => 2,
            ],
            [
                'title' => 'WhatsAppBizAI lance la gestion multi-devises',
                'slug' => 'whatsappbizai-gestion-multi-devises',
                'excerpt' => 'Désormais, facturez en XAF, USD, EUR et 7 autres devises. Une fonctionnalité clé pour les PME qui opèrent à l\'international.',
                'content' => '<h2>Une réponse à un besoin réel</h2>
<p>Nos utilisateurs nous le demandaient depuis longtemps : pouvoir facturer dans différentes devises. Aujourd\'hui, c\'est chose faite.</p>

<h2>Les devises supportées</h2>
<p>WhatsAppBizAI supporte désormais <strong>10 devises</strong> : XAF, USD, EUR, GBP, NGN, KES, GHS, ZAR, CAD et AED.</p>

<h2>Comment ça marche ?</h2>
<p>Choisissez votre devise par défaut dans les paramètres de votre entreprise. Vous pouvez également changer de devise au moment de créer une facture ou un devis.</p>

<h2>Pourquoi c\'est important ?</h2>
<ul>
<li>Facturez vos clients internationaux dans leur devise</li>
<li>Évitez les confusions liées aux taux de change</li>
<li>Professionnalisez votre image face à la diaspora</li>
</ul>

<blockquote>Facturer dans la bonne devise, c\'est respecter vos clients.</blockquote>',
                'category' => 'news',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(14),
                'meta_title' => 'Multi-devises — Actualité WhatsAppBizAI',
                'meta_description' => 'WhatsAppBizAI lance la gestion multi-devises : XAF, USD, EUR et plus pour vos factures et devis.',
                'sort_order' => 3,
            ],
            [
                'title' => 'Comment Tech Solutions Cameroun a doublé son chiffre d\'affaires',
                'slug' => 'cas-client-tech-solutions-cameroun',
                'excerpt' => 'Découvrez comment cette entreprise de développement web à Douala a transformé sa productivité grâce à WhatsAppBizAI.',
                'content' => '<h2>Le défi</h2>
<p>Tech Solutions Cameroun, basée à Douala, gérait manuellement ses devis, factures et relances. Résultat : des heures perdues et des paiements en retard.</p>

<h2>La solution</h2>
<p>Après l\'adoption de WhatsAppBizAI, l\'assistant IA prend en charge l\'ensemble du cycle commercial : du devis au paiement, tout est automatisé via WhatsApp.</p>

<h2>Les résultats en 3 mois</h2>
<ul>
<li><strong>+120%</strong> de devis envoyés par mois</li>
<li><strong>-60%</strong> de temps passé sur la facturation</li>
<li><strong>+85%</strong> de taux de recouvrement</li>
<li><strong>0</strong> facture oubliée</li>
</ul>

<h2>Témoignage</h2>
<blockquote>« Avant WhatsAppBizAI, je passais 10 heures par semaine sur mes factures. Maintenant, je me consacre à mes projets. L\'IA fait tout le reste. » — Happi Olivier, Fondateur</blockquote>

<h2>Vous aussi, transformez votre business</h2>
<p>Commencez votre essai gratuit dès aujourd\'hui. Aucune carte bancaire requise.</p>',
                'category' => 'cas_client',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(21),
                'meta_title' => 'Cas client Tech Solutions Cameroun — Blog WhatsAppBizAI',
                'meta_description' => 'Comment Tech Solutions Cameroun a doublé son CA grâce à l\'automatisation WhatsApp de WhatsAppBizAI.',
                'sort_order' => 4,
            ],
        ];

        foreach ($posts as $data) {
            Post::create($data);
        }
    }
}
