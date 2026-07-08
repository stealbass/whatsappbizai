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
                'title_fr' => 'Comment automatiser vos factures via WhatsApp',
                'title_en' => 'How to automate your invoices via WhatsApp',
                'slug' => 'automatiser-factures-whatsapp',
                'excerpt' => 'Découvrez comment envoyer vos factures automatiquement à vos clients directement sur WhatsApp grâce à l\'intelligence artificielle.',
                'excerpt_fr' => 'Découvrez comment envoyer vos factures automatiquement à vos clients directement sur WhatsApp grâce à l\'intelligence artificielle.',
                'excerpt_en' => 'Learn how to send invoices automatically to your customers directly on WhatsApp using artificial intelligence.',
                'content' => '<h2>Pourquoi automatiser vos factures ?</h2>
<p>Envoyer des factures manuellement prend du temps et expose à des erreurs. Avec WhatsAppBizAI, votre assistant IA génère et envoie les factures automatiquement quand vos clients vous contactent.</p>

<h2>Étape 1 : Configurez vos services</h2>
<p>Renseignez vos prestations, tarifs et délais dans l\'onglet <strong>Services</strong> de votre tableau de bord. L\'IA utilise ces informations pour créer des devis et factures instantanément.</p>

<h2>Étape 2 : Activez l\'envoi WhatsApp</h2>
<p>Dans <strong>Paramètres &gt; WhatsApp</strong>, connectez votre compte WhatsApp Business. L\'assistant IA enverra les factures PDF directement dans la conversation.</p>

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
                'content_fr' => '<h2>Pourquoi automatiser vos factures ?</h2>
<p>Envoyer des factures manuellement prend du temps et expose à des erreurs. Avec WhatsAppBizAI, votre assistant IA génère et envoie les factures automatiquement quand vos clients vous contactent.</p>

<h2>Étape 1 : Configurez vos services</h2>
<p>Renseignez vos prestations, tarifs et délais dans l\'onglet <strong>Services</strong> de votre tableau de bord. L\'IA utilise ces informations pour créer des devis et factures instantanément.</p>

<h2>Étape 2 : Activez l\'envoi WhatsApp</h2>
<p>Dans <strong>Paramètres &gt; WhatsApp</strong>, connectez votre compte WhatsApp Business. L\'assistant IA enverra les factures PDF directement dans la conversation.</p>

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
                'content_en' => '<h2>Why automate your invoices?</h2>
<p>Sending invoices manually is time-consuming and error-prone. With WhatsAppBizAI, your AI assistant generates and sends invoices automatically when your customers contact you.</p>

<h2>Step 1: Set up your services</h2>
<p>Enter your services, rates, and turnaround times in the <strong>Services</strong> tab of your dashboard. The AI uses this information to create quotes and invoices instantly.</p>

<h2>Step 2: Enable WhatsApp sending</h2>
<p>In <strong>Settings &gt; WhatsApp</strong>, connect your WhatsApp Business account. The AI assistant will send PDF invoices directly in the conversation.</p>

<h2>Step 3: Track your payments</h2>
<p>Every sent invoice is automatically tracked. You instantly know whether it has been read, accepted, or paid.</p>

<blockquote>Automating your invoices saves you an average of 5 hours per week.</blockquote>

<h2>Benefits</h2>
<ul>
<li>Instant sending via WhatsApp</li>
<li>Professional PDF generated automatically</li>
<li>Real-time status tracking</li>
<li>Automatic reminders for unpaid invoices</li>
</ul>',
                'category' => 'tutorial',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'meta_title' => 'Automatiser factures WhatsApp — Tutoriel WhatsAppBizAI',
                'meta_title_fr' => 'Automatiser factures WhatsApp — Tutoriel WhatsAppBizAI',
                'meta_title_en' => 'Automate WhatsApp invoices — WhatsAppBizAI Tutorial',
                'meta_description' => 'Apprenez à automatiser l\'envoi de factures à vos clients via WhatsApp avec l\'assistant IA de WhatsAppBizAI.',
                'meta_description_fr' => 'Apprenez à automatiser l\'envoi de factures à vos clients via WhatsApp avec l\'assistant IA de WhatsAppBizAI.',
                'meta_description_en' => 'Learn how to automate sending invoices to your customers via WhatsApp with the WhatsAppBizAI AI assistant.',
                'sort_order' => 1,
            ],
            [
                'title' => '5 conseils pour mieux gérer vos contacts clients',
                'title_fr' => '5 conseils pour mieux gérer vos contacts clients',
                'title_en' => '5 tips for better customer contact management',
                'slug' => '5-conseils-gestion-contacts-clients',
                'excerpt' => 'Un CRM adapté aux PME africaines : comment organiser, segmenter et fidéliser vos contacts sans outil complexe.',
                'excerpt_fr' => 'Un CRM adapté aux PME africaines : comment organiser, segmenter et fidéliser vos contacts sans outil complexe.',
                'excerpt_en' => 'A CRM designed for African SMEs: how to organize, segment, and retain your contacts without a complex tool.',
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
                'content_fr' => '<h2>1. Centralisez vos contacts</h2>
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
                'content_en' => '<h2>1. Centralize your contacts</h2>
<p>Stop searching through your WhatsApp conversations. Import all your contacts into WhatsAppBizAI with one click using CSV import.</p>

<h2>2. Segment your database</h2>
<p>Use statuses (customer, prospect, supplier) to organize your contacts. This allows you to send targeted messages.</p>

<h2>3. Log interactions</h2>
<p>Every conversation is automatically recorded. You always know what you discussed with each contact.</p>

<h2>4. Send personalized follow-ups</h2>
<p>Our follow-up tool generates personalized messages using AI, based on each contact\'s history.</p>

<h2>5. Track engagement</h2>
<p>Know which contacts are active, which need follow-up, and prioritize your time commercially.</p>

<blockquote>A well-organized contact = a loyal customer.</blockquote>',
                'category' => 'astuce',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(7),
                'meta_title' => '5 conseils gestion contacts clients PME — Blog WhatsAppBizAI',
                'meta_title_fr' => '5 conseils gestion contacts clients PME — Blog WhatsAppBizAI',
                'meta_title_en' => '5 tips for SME customer contact management — WhatsAppBizAI Blog',
                'meta_description' => 'Découvrez 5 astuces pour mieux gérer vos contacts clients et booster votre fidélisation avec WhatsAppBizAI.',
                'meta_description_fr' => 'Découvrez 5 astuces pour mieux gérer vos contacts clients et booster votre fidélisation avec WhatsAppBizAI.',
                'meta_description_en' => 'Discover 5 tips to better manage your customer contacts and boost retention with WhatsAppBizAI.',
                'sort_order' => 2,
            ],
            [
                'title' => 'WhatsAppBizAI lance la gestion multi-devises',
                'title_fr' => 'WhatsAppBizAI lance la gestion multi-devises',
                'title_en' => 'WhatsAppBizAI launches multi-currency management',
                'slug' => 'whatsappbizai-gestion-multi-devises',
                'excerpt' => 'Désormais, facturez en XAF, USD, EUR et 7 autres devises. Une fonctionnalité clé pour les PME qui opèrent à l\'international.',
                'excerpt_fr' => 'Désormais, facturez en XAF, USD, EUR et 7 autres devises. Une fonctionnalité clé pour les PME qui opèrent à l\'international.',
                'excerpt_en' => 'Now invoice in XAF, USD, EUR, and 7 other currencies. A key feature for SMEs operating internationally.',
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
                'content_fr' => '<h2>Une réponse à un besoin réel</h2>
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
                'content_en' => '<h2>A response to a real need</h2>
<p>Our users had been asking for it for a long time: the ability to invoice in different currencies. Today, it\'s a reality.</p>

<h2>Supported currencies</h2>
<p>WhatsAppBizAI now supports <strong>10 currencies</strong>: XAF, USD, EUR, GBP, NGN, KES, GHS, ZAR, CAD, and AED.</p>

<h2>How does it work?</h2>
<p>Choose your default currency in your business settings. You can also change currency when creating an invoice or quote.</p>

<h2>Why is this important?</h2>
<ul>
<li>Invoice your international customers in their currency</li>
<li>Avoid confusion related to exchange rates</li>
<li>Professionalize your image with the diaspora</li>
</ul>

<blockquote>Invoicing in the right currency means respecting your customers.</blockquote>',
                'category' => 'news',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(14),
                'meta_title' => 'Multi-devises — Actualité WhatsAppBizAI',
                'meta_title_fr' => 'Multi-devises — Actualité WhatsAppBizAI',
                'meta_title_en' => 'Multi-currency — WhatsAppBizAI News',
                'meta_description' => 'WhatsAppBizAI lance la gestion multi-devises : XAF, USD, EUR et plus pour vos factures et devis.',
                'meta_description_fr' => 'WhatsAppBizAI lance la gestion multi-devises : XAF, USD, EUR et plus pour vos factures et devis.',
                'meta_description_en' => 'WhatsAppBizAI launches multi-currency management: XAF, USD, EUR and more for your invoices and quotes.',
                'sort_order' => 3,
            ],
            [
                'title' => 'Comment Tech Solutions Cameroun a doublé son chiffre d\'affaires',
                'title_fr' => 'Comment Tech Solutions Cameroun a doublé son chiffre d\'affaires',
                'title_en' => 'How Tech Solutions Cameroon doubled its revenue',
                'slug' => 'cas-client-tech-solutions-cameroun',
                'excerpt' => 'Découvrez comment cette entreprise de développement web à Douala a transformé sa productivité grâce à WhatsAppBizAI.',
                'excerpt_fr' => 'Découvrez comment cette entreprise de développement web à Douala a transformé sa productivité grâce à WhatsAppBizAI.',
                'excerpt_en' => 'Discover how this web development company in Douala transformed its productivity with WhatsAppBizAI.',
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
                'content_fr' => '<h2>Le défi</h2>
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
                'content_en' => '<h2>The challenge</h2>
<p>Tech Solutions Cameroon, based in Douala, was manually managing its quotes, invoices, and follow-ups. Result: hours lost and late payments.</p>

<h2>The solution</h2>
<p>After adopting WhatsAppBizAI, the AI assistant handles the entire business cycle: from quote to payment, everything is automated via WhatsApp.</p>

<h2>Results in 3 months</h2>
<ul>
<li><strong>+120%</strong> quotes sent per month</li>
<li><strong>-60%</strong> time spent on billing</li>
<li><strong>+85%</strong> recovery rate</li>
<li><strong>0</strong> forgotten invoices</li>
</ul>

<h2>Testimonial</h2>
<blockquote>"Before WhatsAppBizAI, I spent 10 hours a week on my invoices. Now I focus on my projects. The AI handles everything else." — Happi Olivier, Founder</blockquote>

<h2>You too, transform your business</h2>
<p>Start your free trial today. No credit card required.</p>',
                'category' => 'cas_client',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(21),
                'meta_title' => 'Cas client Tech Solutions Cameroun — Blog WhatsAppBizAI',
                'meta_title_fr' => 'Cas client Tech Solutions Cameroun — Blog WhatsAppBizAI',
                'meta_title_en' => 'Customer story: Tech Solutions Cameroon — WhatsAppBizAI Blog',
                'meta_description' => 'Comment Tech Solutions Cameroun a doublé son CA grâce à l\'automatisation WhatsApp de WhatsAppBizAI.',
                'meta_description_fr' => 'Comment Tech Solutions Cameroun a doublé son CA grâce à l\'automatisation WhatsApp de WhatsAppBizAI.',
                'meta_description_en' => 'How Tech Solutions Cameroon doubled its revenue with WhatsAppBizAI WhatsApp automation.',
                'sort_order' => 4,
            ],
        ];

        foreach ($posts as $data) {
            Post::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
