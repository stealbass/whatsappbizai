<?php

namespace Database\Seeders;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Database\Seeder;

class HelpSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'getting-started',
                'icon' => '🚀',
                'color' => '#0ea5e9',
                'sort_order' => 1,
                'is_active' => true,
                'name_fr' => 'Prise en main',
                'name_en' => 'Getting Started',
                'description_fr' => 'Comprendre WhatsAppBizAI, configurer votre compte et démarrer rapidement avec un espace de travail opérationnel.',
                'description_en' => 'Understand WhatsAppBizAI, configure your account, and get productive quickly with a ready-to-use workspace.',
            ],
            [
                'slug' => 'crm-contacts',
                'icon' => '👥',
                'color' => '#8b5cf6',
                'sort_order' => 2,
                'is_active' => true,
                'name_fr' => 'CRM & contacts',
                'name_en' => 'CRM & Contacts',
                'description_fr' => 'Importer, segmenter, suivre et relancer vos prospects et clients directement depuis votre CRM connecté à WhatsApp.',
                'description_en' => 'Import, segment, track, and follow up with prospects and customers directly from your WhatsApp-connected CRM.',
            ],
            [
                'slug' => 'quotes-invoices-payments',
                'icon' => '💼',
                'color' => '#14b8a6',
                'sort_order' => 3,
                'is_active' => true,
                'name_fr' => 'Devis, factures & paiements',
                'name_en' => 'Quotes, Invoices & Payments',
                'description_fr' => 'Créer des devis, envoyer des factures, suivre les règlements et accélérer votre cycle de vente.',
                'description_en' => 'Create quotes, send invoices, track payments, and speed up your sales cycle.',
            ],
            [
                'slug' => 'automation-ai',
                'icon' => '🤖',
                'color' => '#f59e0b',
                'sort_order' => 4,
                'is_active' => true,
                'name_fr' => 'Automatisation & IA',
                'name_en' => 'Automation & AI',
                'description_fr' => 'Exploiter l’assistant IA, les relances intelligentes et les automatisations pour gagner du temps sans perdre la touche humaine.',
                'description_en' => 'Use the AI assistant, smart follow-ups, and automations to save time without losing the human touch.',
            ],
        ];

        foreach ($categories as $categoryData) {
            HelpCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $categoryIds = HelpCategory::query()->pluck('id', 'slug');

        $articles = [
            [
                'help_category_id' => $categoryIds['getting-started'],
                'slug' => 'what-is-whatsappbizai',
                'type' => 'article',
                'title_fr' => 'Qu’est-ce que WhatsAppBizAI et à qui s’adresse la plateforme ?',
                'title_en' => 'What is WhatsAppBizAI and who is it for?',
                'excerpt_fr' => 'Une vue d’ensemble claire de la plateforme, de ses cas d’usage et de la manière dont elle aide les indépendants et PME à vendre plus vite.',
                'excerpt_en' => 'A clear overview of the platform, its use cases, and how it helps freelancers and small businesses sell faster.',
                'content_fr' => <<<'HTML'
<h2>Une plateforme pensée pour les ventes conversationnelles</h2>
<p>WhatsAppBizAI aide les entreprises qui gèrent leurs opportunités commerciales sur WhatsApp à structurer leur activité sans quitter leurs conversations. Au lieu d’alterner entre plusieurs outils, vous centralisez vos contacts, devis, factures, suivis et relances dans une seule interface.</p>
<p>La plateforme a été pensée pour les freelances, agences, prestataires de services, commerces, consultants et petites équipes commerciales qui ont besoin de répondre vite, de rester organisés et d’éviter les oublis.</p>

<h2>À quoi sert concrètement WhatsAppBizAI ?</h2>
<ul>
<li>Conserver un historique client exploitable.</li>
<li>Créer rapidement des devis professionnels.</li>
<li>Transformer un devis accepté en facture en quelques clics.</li>
<li>Suivre les paiements, relancer les clients et réduire les impayés.</li>
<li>Utiliser l’IA pour rédiger, reformuler et personnaliser des messages commerciaux.</li>
</ul>

<h2>Pourquoi ce modèle fonctionne bien pour les PME</h2>
<p>Beaucoup de petites entreprises vendent déjà via WhatsApp, mais sans véritable système. Les messages s’accumulent, les demandes urgentes passent entre les mailles du filet et les relances se font tardivement. WhatsAppBizAI apporte une couche d’organisation et d’automatisation sans casser vos habitudes.</p>
<p>Vous conservez la proximité du canal WhatsApp, tout en ajoutant une vraie logique CRM et facturation derrière.</p>

<h2>Les bénéfices les plus visibles</h2>
<ul>
<li>Réduction du temps administratif.</li>
<li>Meilleure réactivité commerciale.</li>
<li>Moins d’erreurs dans les devis et factures.</li>
<li>Meilleur suivi des prospects chauds.</li>
<li>Expérience client plus professionnelle.</li>
</ul>

<h2>Par où commencer ?</h2>
<p>Le plus simple est de configurer votre entreprise, créer vos premiers services, importer vos contacts et tester la création d’un devis. Une fois ces bases en place, vous pouvez activer les fonctions d’IA et de relance pour accélérer vos ventes.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>A platform built for conversational sales</h2>
<p>WhatsAppBizAI helps businesses that manage sales opportunities on WhatsApp bring structure to their workflow without leaving conversations. Instead of switching between multiple tools, you centralize contacts, quotes, invoices, follow-ups, and reminders in one interface.</p>
<p>The platform is designed for freelancers, agencies, service providers, consultants, retail businesses, and small sales teams that need to reply quickly, stay organized, and avoid missed opportunities.</p>

<h2>What does WhatsAppBizAI actually help you do?</h2>
<ul>
<li>Keep a usable client history.</li>
<li>Create professional quotes quickly.</li>
<li>Turn an accepted quote into an invoice in a few clicks.</li>
<li>Track payments, follow up with clients, and reduce overdue invoices.</li>
<li>Use AI to draft, rewrite, and personalize sales messages.</li>
</ul>

<h2>Why this works well for small businesses</h2>
<p>Many small businesses already sell through WhatsApp, but without a real system. Messages pile up, urgent requests slip through the cracks, and follow-ups happen too late. WhatsAppBizAI adds organization and automation without forcing you to change how you sell.</p>
<p>You keep the closeness of WhatsApp while adding real CRM and invoicing logic behind it.</p>

<h2>Most visible benefits</h2>
<ul>
<li>Less admin time.</li>
<li>Faster commercial response time.</li>
<li>Fewer quote and invoice errors.</li>
<li>Better follow-up on warm leads.</li>
<li>A more professional customer experience.</li>
</ul>

<h2>Where should you start?</h2>
<p>The easiest path is to configure your business, create your first services, import your contacts, and test a quote. Once those basics are in place, you can enable AI and follow-up features to accelerate sales.</p>
HTML,
                'meta_title_fr' => 'Qu’est-ce que WhatsAppBizAI ? Guide complet pour PME et freelances',
                'meta_title_en' => 'What is WhatsAppBizAI? Complete guide for SMEs and freelancers',
                'meta_description_fr' => 'Découvrez à quoi sert WhatsAppBizAI, pour qui la plateforme est conçue et comment elle aide les PME à gérer CRM, devis, factures et relances sur WhatsApp.',
                'meta_description_en' => 'Learn what WhatsAppBizAI does, who it is built for, and how it helps small businesses manage CRM, quotes, invoices, and follow-ups on WhatsApp.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(20),
                'sort_order' => 1,
                'views' => 220,
                'difficulty' => 'beginner',
                'reading_minutes' => 5,
                'steps' => null,
            ],
            [
                'help_category_id' => $categoryIds['getting-started'],
                'slug' => 'configure-your-business-profile',
                'type' => 'tutorial',
                'title_fr' => 'Configurer votre profil entreprise pour partir sur de bonnes bases',
                'title_en' => 'Configure your business profile the right way',
                'excerpt_fr' => 'Un tutoriel pas à pas pour renseigner votre activité, vos coordonnées, votre devise et vos paramètres essentiels.',
                'excerpt_en' => 'A step-by-step tutorial to set up your business details, currency, contact information, and essential settings.',
                'content_fr' => <<<'HTML'
<h2>Pourquoi cette configuration est importante</h2>
<p>Votre profil entreprise alimente une grande partie des informations utilisées dans vos devis, factures, exports et messages assistés par IA. Une configuration propre évite des corrections manuelles plus tard.</p>

<h2>Ce que vous devez préparer avant de commencer</h2>
<ul>
<li>Nom commercial exact.</li>
<li>Nom du responsable ou du signataire.</li>
<li>Adresse email principale.</li>
<li>Numéro de téléphone ou WhatsApp professionnel.</li>
<li>Ville, pays et fuseau horaire.</li>
<li>Devise de facturation principale.</li>
</ul>

<h2>Bonnes pratiques</h2>
<p>Choisissez une devise cohérente avec votre marché principal. Vérifiez votre fuseau horaire pour éviter les erreurs sur les dates d’échéance. Utilisez une adresse email réellement surveillée afin que les réponses clients ne soient pas perdues.</p>

<h2>Après la configuration</h2>
<p>Une fois votre profil enregistré, créez vos services ou prestations standards. Cela accélèrera énormément la génération de devis et de factures.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>Why this setup matters</h2>
<p>Your business profile powers much of the information used in quotes, invoices, exports, and AI-assisted messages. Setting it up correctly now saves a lot of manual correction later.</p>

<h2>What to prepare first</h2>
<ul>
<li>Your exact business name.</li>
<li>The owner or signatory name.</li>
<li>Your main email address.</li>
<li>Your professional phone or WhatsApp number.</li>
<li>City, country, and timezone.</li>
<li>Your default billing currency.</li>
</ul>

<h2>Best practices</h2>
<p>Choose a currency that matches your main market. Double-check your timezone to avoid due-date mistakes. Use an email address that is actively monitored so customer replies are not missed.</p>

<h2>After setup</h2>
<p>Once the profile is saved, create your standard services or offers. That makes quote and invoice generation much faster.</p>
HTML,
                'meta_title_fr' => 'Configurer le profil entreprise sur WhatsAppBizAI',
                'meta_title_en' => 'How to configure your business profile in WhatsAppBizAI',
                'meta_description_fr' => 'Tutoriel pour configurer le profil entreprise, la devise, les coordonnées et les paramètres clés dans WhatsAppBizAI.',
                'meta_description_en' => 'Tutorial for configuring your business profile, currency, contact details, and key settings in WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(18),
                'sort_order' => 2,
                'views' => 180,
                'difficulty' => 'beginner',
                'reading_minutes' => 6,
                'steps' => [
                    [
                        'title_fr' => 'Ouvrez les paramètres entreprise',
                        'title_en' => 'Open business settings',
                        'description_fr' => 'Depuis votre tableau de bord, accédez à la section des paramètres de votre entreprise.',
                        'description_en' => 'From your dashboard, open the business settings section.',
                        'icon' => '⚙️',
                    ],
                    [
                        'title_fr' => 'Renseignez les informations légales et commerciales',
                        'title_en' => 'Fill in legal and commercial details',
                        'description_fr' => 'Ajoutez le nom de l’entreprise, le responsable, l’email et le numéro principal.',
                        'description_en' => 'Add the company name, owner, email, and main contact number.',
                        'icon' => '🏢',
                    ],
                    [
                        'title_fr' => 'Choisissez la devise et le fuseau horaire',
                        'title_en' => 'Choose currency and timezone',
                        'description_fr' => 'Définissez la devise de facturation et le fuseau horaire adaptés à votre activité.',
                        'description_en' => 'Set the billing currency and timezone that best fit your operations.',
                        'icon' => '💱',
                    ],
                    [
                        'title_fr' => 'Enregistrez et vérifiez vos documents',
                        'title_en' => 'Save and verify your documents',
                        'description_fr' => 'Testez ensuite un devis ou une facture pour contrôler le rendu final.',
                        'description_en' => 'Then test a quote or invoice to verify the final output.',
                        'icon' => '✅',
                    ],
                ],
            ],
            [
                'help_category_id' => $categoryIds['getting-started'],
                'slug' => 'first-day-setup-checklist',
                'type' => 'guide',
                'title_fr' => 'Checklist interactive : réussir votre premier jour sur WhatsAppBizAI',
                'title_en' => 'Interactive checklist: make your first day on WhatsAppBizAI a success',
                'excerpt_fr' => 'Une checklist actionnable pour mettre en place votre compte, vos services et votre premier flux commercial en moins d’une heure.',
                'excerpt_en' => 'An actionable checklist to set up your account, services, and first sales workflow in under an hour.',
                'content_fr' => <<<'HTML'
<h2>Objectif de cette checklist</h2>
<p>Cette checklist vous aide à passer d’un compte fraîchement créé à un espace de travail prêt à l’usage. Elle est idéale si vous voulez obtenir un résultat concret rapidement, sans passer trop de temps à explorer chaque menu.</p>

<h2>Comment utiliser ce guide</h2>
<p>Suivez les étapes dans l’ordre. À chaque étape, validez l’action réalisée. Lorsque toutes les étapes sont terminées, vous aurez une base propre pour commencer à vendre, relancer et facturer.</p>

<h2>Conseil pratique</h2>
<p>Ne cherchez pas la perfection dès le départ. Le plus important est d’avoir un système fonctionnel. Vous pourrez enrichir vos catégories, modèles de messages et paramètres avancés ensuite.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>The goal of this checklist</h2>
<p>This checklist helps you move from a newly created account to a usable workspace. It is ideal if you want a concrete result quickly without spending too much time exploring every menu.</p>

<h2>How to use this guide</h2>
<p>Follow the steps in order. Mark each step as done once completed. By the end, you will have a clean foundation to start selling, following up, and invoicing.</p>

<h2>Practical advice</h2>
<p>Do not aim for perfection on day one. The priority is to have a system that works. You can refine categories, message templates, and advanced settings later.</p>
HTML,
                'meta_title_fr' => 'Checklist premier jour WhatsAppBizAI',
                'meta_title_en' => 'WhatsAppBizAI first-day setup checklist',
                'meta_description_fr' => 'Guide interactif pour configurer votre compte WhatsAppBizAI, vos services et vos premiers flux en moins d’une heure.',
                'meta_description_en' => 'Interactive guide to set up your WhatsAppBizAI account, services, and first workflows in under an hour.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(16),
                'sort_order' => 3,
                'views' => 140,
                'difficulty' => 'beginner',
                'reading_minutes' => 7,
                'steps' => [
                    ['title_fr' => 'Configurer le profil entreprise', 'title_en' => 'Set up the business profile', 'description_fr' => 'Complétez les informations de base de votre activité.', 'description_en' => 'Complete your core business details.', 'icon' => '🏁'],
                    ['title_fr' => 'Créer vos services principaux', 'title_en' => 'Create your main services', 'description_fr' => 'Ajoutez vos offres les plus vendues avec leurs tarifs.', 'description_en' => 'Add your most common offers with pricing.', 'icon' => '🧩'],
                    ['title_fr' => 'Importer ou créer quelques contacts', 'title_en' => 'Import or create a few contacts', 'description_fr' => 'Commencez avec de vrais prospects ou clients tests.', 'description_en' => 'Start with real prospects or test customers.', 'icon' => '👥'],
                    ['title_fr' => 'Créer un premier devis', 'title_en' => 'Create your first quote', 'description_fr' => 'Vérifiez le rendu et la clarté du document.', 'description_en' => 'Check the output and clarity of the document.', 'icon' => '🧾'],
                    ['title_fr' => 'Tester une relance ou un message IA', 'title_en' => 'Test an AI message or follow-up', 'description_fr' => 'Validez le ton et l’utilité de l’assistant.', 'description_en' => 'Validate the tone and usefulness of the assistant.', 'icon' => '🤖'],
                ],
            ],
            [
                'help_category_id' => $categoryIds['crm-contacts'],
                'slug' => 'organize-contact-pipeline',
                'type' => 'article',
                'title_fr' => 'Comment organiser un pipeline simple avec vos contacts',
                'title_en' => 'How to organize a simple pipeline with your contacts',
                'excerpt_fr' => 'Une méthode pragmatique pour distinguer prospects froids, opportunités chaudes et clients actifs dans votre CRM.',
                'excerpt_en' => 'A practical way to separate cold leads, warm opportunities, and active customers in your CRM.',
                'content_fr' => <<<'HTML'
<h2>Pourquoi la segmentation est essentielle</h2>
<p>Un bon CRM n’est pas seulement un carnet d’adresses numérique. C’est un outil de décision. Lorsque vos contacts sont bien rangés, vous savez immédiatement qui relancer, qui convertir et qui fidéliser.</p>

<h2>Une structure simple qui fonctionne</h2>
<ul>
<li><strong>Prospect</strong> : la personne a manifesté un intérêt, mais rien n’est encore engagé.</li>
<li><strong>Client</strong> : une vente a déjà eu lieu ou une relation commerciale est active.</li>
<li><strong>À relancer</strong> : le contact était chaud, mais la conversation s’est arrêtée.</li>
<li><strong>Inactif</strong> : pas d’échange utile depuis un certain temps.</li>
</ul>

<h2>Ce qu’il faut éviter</h2>
<p>Ne créez pas trop de statuts au départ. Un pipeline trop complexe ralentit vos équipes. Mieux vaut quatre statuts vraiment utilisés qu’une dizaine laissés à l’abandon.</p>

<h2>Mesurez l’utilité du système</h2>
<p>Si votre segmentation vous aide à décider qui contacter aujourd’hui, elle est bonne. Si elle vous oblige à trop réfléchir, simplifiez-la.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>Why segmentation matters</h2>
<p>A good CRM is more than a digital address book. It is a decision tool. When your contacts are organized well, you instantly know who to follow up with, who to convert, and who to retain.</p>

<h2>A simple structure that works</h2>
<ul>
<li><strong>Prospect</strong>: the person has shown interest, but nothing is committed yet.</li>
<li><strong>Customer</strong>: a sale has happened already or the relationship is active.</li>
<li><strong>Needs follow-up</strong>: the contact was warm, but the conversation stalled.</li>
<li><strong>Inactive</strong>: no meaningful exchange for a while.</li>
</ul>

<h2>What to avoid</h2>
<p>Do not create too many statuses at the start. An overly complex pipeline slows teams down. Four real, used statuses are better than ten abandoned ones.</p>

<h2>How to judge the system</h2>
<p>If your segmentation helps you decide who to contact today, it is working. If it makes you think too hard, simplify it.</p>
HTML,
                'meta_title_fr' => 'Organiser ses contacts CRM sur WhatsAppBizAI',
                'meta_title_en' => 'Organize CRM contacts in WhatsAppBizAI',
                'meta_description_fr' => 'Apprenez à organiser vos prospects et clients avec un pipeline simple et efficace dans WhatsAppBizAI.',
                'meta_description_en' => 'Learn how to organize leads and customers with a simple, efficient pipeline in WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(15),
                'sort_order' => 1,
                'views' => 125,
                'difficulty' => 'beginner',
                'reading_minutes' => 4,
                'steps' => null,
            ],
            [
                'help_category_id' => $categoryIds['crm-contacts'],
                'slug' => 'import-contacts-from-csv',
                'type' => 'tutorial',
                'title_fr' => 'Importer vos contacts depuis un fichier CSV sans désordre',
                'title_en' => 'Import contacts from CSV without creating a mess',
                'excerpt_fr' => 'Le bon format, les champs à vérifier et les erreurs à éviter pour un import propre.',
                'excerpt_en' => 'The right format, fields to check, and mistakes to avoid for a clean import.',
                'content_fr' => <<<'HTML'
<h2>Avant l’import : nettoyez votre fichier</h2>
<p>Un import réussi commence dans votre tableur. Supprimez les doublons, uniformisez les numéros de téléphone et vérifiez que chaque ligne correspond à une seule personne ou entreprise.</p>

<h2>Colonnes recommandées</h2>
<ul>
<li>Nom</li>
<li>Numéro WhatsApp</li>
<li>Email</li>
<li>Statut</li>
<li>Ville ou marché</li>
</ul>

<h2>Vérifications importantes</h2>
<p>Les numéros doivent être au format international si possible. Les statuts doivent suivre une logique cohérente. Évitez les colonnes inutiles qui alourdissent l’import sans servir à vos actions commerciales.</p>

<h2>Après l’import</h2>
<p>Contrôlez un échantillon de contacts dans l’interface. Si les noms et numéros sont propres, vous pouvez continuer avec une segmentation plus fine ou lancer des relances ciblées.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>Before import: clean your file</h2>
<p>A successful import starts in your spreadsheet. Remove duplicates, standardize phone numbers, and make sure each row represents only one person or company.</p>

<h2>Recommended columns</h2>
<ul>
<li>Name</li>
<li>WhatsApp number</li>
<li>Email</li>
<li>Status</li>
<li>City or market</li>
</ul>

<h2>Important checks</h2>
<p>Numbers should be in international format whenever possible. Statuses should follow a consistent logic. Avoid useless columns that make the import heavier without helping your sales actions.</p>

<h2>After import</h2>
<p>Review a sample of contacts in the interface. If names and numbers look clean, you can move on to deeper segmentation or targeted follow-ups.</p>
HTML,
                'meta_title_fr' => 'Importer des contacts CSV dans WhatsAppBizAI',
                'meta_title_en' => 'Import CSV contacts into WhatsAppBizAI',
                'meta_description_fr' => 'Tutoriel pour importer proprement vos contacts CSV dans WhatsAppBizAI sans erreurs ni doublons.',
                'meta_description_en' => 'Tutorial for importing CSV contacts into WhatsAppBizAI cleanly without errors or duplicates.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(13),
                'sort_order' => 2,
                'views' => 132,
                'difficulty' => 'beginner',
                'reading_minutes' => 5,
                'steps' => [
                    ['title_fr' => 'Préparer le fichier CSV', 'title_en' => 'Prepare the CSV file', 'description_fr' => 'Nettoyez les données et supprimez les doublons.', 'description_en' => 'Clean the data and remove duplicates.', 'icon' => '🧹'],
                    ['title_fr' => 'Vérifier les colonnes clés', 'title_en' => 'Verify key columns', 'description_fr' => 'Assurez-vous que les noms et numéros sont bien remplis.', 'description_en' => 'Make sure names and phone numbers are filled correctly.', 'icon' => '📋'],
                    ['title_fr' => 'Lancer l’import', 'title_en' => 'Run the import', 'description_fr' => 'Chargez le fichier depuis la section import des contacts.', 'description_en' => 'Upload the file from the contact import section.', 'icon' => '⬆️'],
                    ['title_fr' => 'Contrôler les résultats', 'title_en' => 'Review the results', 'description_fr' => 'Vérifiez un petit échantillon avant d’aller plus loin.', 'description_en' => 'Check a small sample before moving forward.', 'icon' => '🔎'],
                ],
            ],
            [
                'help_category_id' => $categoryIds['crm-contacts'],
                'slug' => 'lead-follow-up-checklist',
                'type' => 'guide',
                'title_fr' => 'Guide interactif : ne laisser aucun prospect chaud sans relance',
                'title_en' => 'Interactive guide: never leave a warm lead without follow-up',
                'excerpt_fr' => 'Un cadre simple pour relancer au bon moment, avec le bon message et sans harceler vos prospects.',
                'excerpt_en' => 'A simple framework to follow up at the right time, with the right message, without annoying prospects.',
                'content_fr' => <<<'HTML'
<h2>Pourquoi les relances ratent souvent</h2>
<p>Dans beaucoup de petites équipes, les relances dépendent de la mémoire ou du courage du moment. Résultat : soit rien n’est envoyé, soit le message arrive trop tard. Ce guide vous aide à créer une routine plus fiable.</p>

<h2>La logique à adopter</h2>
<p>Chaque relance doit partir d’un contexte clair : intérêt exprimé, devis envoyé, silence après proposition, ou échéance proche. L’objectif n’est pas de pousser au hasard, mais de débloquer une décision.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>Why follow-ups often fail</h2>
<p>In many small teams, follow-ups depend on memory or motivation in the moment. The result: either nothing gets sent, or the message arrives too late. This guide helps you build a more reliable routine.</p>

<h2>The right mindset</h2>
<p>Every follow-up should come from a clear context: expressed interest, quote sent, silence after a proposal, or an upcoming deadline. The goal is not random pressure, but helping a decision move forward.</p>
HTML,
                'meta_title_fr' => 'Guide de relance prospects sur WhatsAppBizAI',
                'meta_title_en' => 'Lead follow-up guide in WhatsAppBizAI',
                'meta_description_fr' => 'Guide interactif pour relancer vos prospects chauds au bon moment dans WhatsAppBizAI.',
                'meta_description_en' => 'Interactive guide for following up with warm leads at the right time in WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(11),
                'sort_order' => 3,
                'views' => 111,
                'difficulty' => 'intermediate',
                'reading_minutes' => 6,
                'steps' => [
                    ['title_fr' => 'Identifier les prospects silencieux', 'title_en' => 'Identify silent warm leads', 'description_fr' => 'Repérez les conversations où l’intérêt existait mais aucune décision n’a suivi.', 'description_en' => 'Spot conversations where interest existed but no decision followed.', 'icon' => '🔥'],
                    ['title_fr' => 'Relire le dernier contexte', 'title_en' => 'Review the latest context', 'description_fr' => 'Vérifiez l’historique pour relancer de manière pertinente.', 'description_en' => 'Review message history before following up.', 'icon' => '🧠'],
                    ['title_fr' => 'Rédiger un message court', 'title_en' => 'Write a short message', 'description_fr' => 'Privilégiez la clarté, une seule question et un ton humain.', 'description_en' => 'Prefer clarity, one question, and a human tone.', 'icon' => '✍️'],
                    ['title_fr' => 'Planifier la prochaine action', 'title_en' => 'Plan the next action', 'description_fr' => 'Décidez quand relancer à nouveau ou quand classer le prospect.', 'description_en' => 'Decide when to follow up again or when to close the lead.', 'icon' => '📅'],
                ],
            ],
            [
                'help_category_id' => $categoryIds['quotes-invoices-payments'],
                'slug' => 'quote-to-invoice-workflow',
                'type' => 'article',
                'title_fr' => 'Du devis à la facture : construire un flux commercial propre',
                'title_en' => 'From quote to invoice: build a clean sales workflow',
                'excerpt_fr' => 'Comment fluidifier vos documents commerciaux pour accélérer les signatures et les encaissements.',
                'excerpt_en' => 'How to streamline your commercial documents to accelerate approvals and payments.',
                'content_fr' => <<<'HTML'
<h2>Pourquoi le flux complet compte plus que le document isolé</h2>
<p>Un devis parfait ne sert pas à grand-chose si son suivi est mauvais. Une facture claire arrive trop tard si la validation client traîne. Le vrai gain vient d’un système où chaque document prépare l’étape suivante.</p>

<h2>Le flux recommandé</h2>
<ol>
<li>Créer un devis clair avec un périmètre bien défini.</li>
<li>Envoyer le document rapidement après l’échange commercial.</li>
<li>Relancer si nécessaire avec un rappel contextualisé.</li>
<li>Convertir le devis accepté en facture sans ressaisie.</li>
<li>Suivre le paiement jusqu’à encaissement.</li>
</ol>

<h2>Les erreurs à éviter</h2>
<ul>
<li>Modifier les prix manuellement à chaque étape.</li>
<li>Changer le vocabulaire entre le devis et la facture.</li>
<li>Attendre trop longtemps avant de relancer.</li>
<li>Oublier les échéances de paiement.</li>
</ul>
HTML,
                'content_en' => <<<'HTML'
<h2>Why the full workflow matters more than a single document</h2>
<p>A perfect quote is not that useful if the follow-up is weak. A clear invoice still arrives too late if customer approval drags on. The real value comes from a system where each document prepares the next step.</p>

<h2>The recommended workflow</h2>
<ol>
<li>Create a clear quote with a well-defined scope.</li>
<li>Send the document quickly after the sales conversation.</li>
<li>Follow up when needed with contextual reminders.</li>
<li>Convert the accepted quote into an invoice without retyping.</li>
<li>Track payment until collection.</li>
</ol>

<h2>Mistakes to avoid</h2>
<ul>
<li>Editing prices manually at every stage.</li>
<li>Changing wording between quote and invoice.</li>
<li>Waiting too long before following up.</li>
<li>Forgetting payment deadlines.</li>
</ul>
HTML,
                'meta_title_fr' => 'Workflow devis facture sur WhatsAppBizAI',
                'meta_title_en' => 'Quote to invoice workflow in WhatsAppBizAI',
                'meta_description_fr' => 'Créez un flux devis-facture fluide pour accélérer vos ventes et vos paiements avec WhatsAppBizAI.',
                'meta_description_en' => 'Build a smooth quote-to-invoice workflow to speed up sales and payments with WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(10),
                'sort_order' => 1,
                'views' => 118,
                'difficulty' => 'intermediate',
                'reading_minutes' => 5,
                'steps' => null,
            ],
            [
                'help_category_id' => $categoryIds['quotes-invoices-payments'],
                'slug' => 'create-and-send-first-quote',
                'type' => 'tutorial',
                'title_fr' => 'Créer et envoyer votre premier devis professionnel',
                'title_en' => 'Create and send your first professional quote',
                'excerpt_fr' => 'Le tutoriel essentiel pour passer d’une demande client à un devis propre, lisible et rapidement envoyé.',
                'excerpt_en' => 'The essential tutorial to move from a client request to a clean, readable quote sent quickly.',
                'content_fr' => <<<'HTML'
<h2>L’objectif</h2>
<p>Créer un devis ne doit pas devenir une tâche lourde. Avec un bon paramétrage, vous pouvez partir d’une demande reçue sur WhatsApp et envoyer un devis professionnel en quelques minutes.</p>

<h2>Ce qui fait un bon devis</h2>
<ul>
<li>Un périmètre compréhensible.</li>
<li>Des lignes de services claires.</li>
<li>Un total sans ambiguïté.</li>
<li>Une durée de validité.</li>
<li>Une prochaine étape explicite.</li>
</ul>

<h2>Conseil commercial</h2>
<p>Envoyez toujours le devis avec un message d’accompagnement humain. Même si le document est bon, le texte qui l’introduit influence fortement la réponse du prospect.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>The goal</h2>
<p>Creating a quote should not become heavy admin work. With the right setup, you can take a request received on WhatsApp and send a professional quote in minutes.</p>

<h2>What makes a good quote</h2>
<ul>
<li>A clear scope.</li>
<li>Easy-to-understand service lines.</li>
<li>An unambiguous total.</li>
<li>A validity period.</li>
<li>A clear next step.</li>
</ul>

<h2>Sales tip</h2>
<p>Always send a quote with a human message around it. Even if the document is strong, the message that introduces it has a major impact on the prospect’s response.</p>
HTML,
                'meta_title_fr' => 'Créer un devis professionnel sur WhatsAppBizAI',
                'meta_title_en' => 'Create a professional quote in WhatsAppBizAI',
                'meta_description_fr' => 'Tutoriel pour créer un devis professionnel et l’envoyer rapidement depuis WhatsAppBizAI.',
                'meta_description_en' => 'Tutorial for creating a professional quote and sending it quickly from WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(8),
                'sort_order' => 2,
                'views' => 126,
                'difficulty' => 'beginner',
                'reading_minutes' => 6,
                'steps' => [
                    ['title_fr' => 'Choisir le contact', 'title_en' => 'Select the contact', 'description_fr' => 'Associez le devis au bon client ou prospect.', 'description_en' => 'Link the quote to the right client or lead.', 'icon' => '👤'],
                    ['title_fr' => 'Ajouter les services', 'title_en' => 'Add services', 'description_fr' => 'Sélectionnez les prestations adaptées à la demande.', 'description_en' => 'Select the services that match the request.', 'icon' => '🧾'],
                    ['title_fr' => 'Vérifier les montants', 'title_en' => 'Review totals', 'description_fr' => 'Contrôlez la devise, les quantités et le total final.', 'description_en' => 'Check the currency, quantities, and final total.', 'icon' => '💰'],
                    ['title_fr' => 'Envoyer le devis', 'title_en' => 'Send the quote', 'description_fr' => 'Partagez-le avec un message d’accompagnement clair.', 'description_en' => 'Send it with a clear supporting message.', 'icon' => '📤'],
                ],
            ],
            [
                'help_category_id' => $categoryIds['quotes-invoices-payments'],
                'slug' => 'invoice-payment-collection-checklist',
                'type' => 'guide',
                'title_fr' => 'Guide interactif : sécuriser le paiement après envoi de facture',
                'title_en' => 'Interactive guide: secure payment after sending an invoice',
                'excerpt_fr' => 'Une checklist simple pour éviter qu’une facture envoyée tombe dans l’oubli et dégrade votre trésorerie.',
                'excerpt_en' => 'A simple checklist to prevent sent invoices from being forgotten and hurting cash flow.',
                'content_fr' => <<<'HTML'
<h2>Le vrai sujet n’est pas l’envoi</h2>
<p>Beaucoup d’entreprises pensent que le travail est terminé une fois la facture envoyée. En réalité, c’est là que le suivi commence. Une bonne discipline de relance améliore fortement l’encaissement.</p>

<h2>La logique du guide</h2>
<p>Ce guide vous aide à vérifier la réception, cadrer l’échéance, relancer avec tact et archiver correctement le dossier une fois payé.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>Sending is not the finish line</h2>
<p>Many businesses assume the job is done once the invoice is sent. In reality, that is where follow-up starts. Good reminder discipline has a strong impact on collection rates.</p>

<h2>The logic behind this guide</h2>
<p>This guide helps you confirm receipt, frame the due date, follow up tactfully, and archive the case properly once payment is received.</p>
HTML,
                'meta_title_fr' => 'Checklist de suivi paiement facture',
                'meta_title_en' => 'Invoice payment follow-up checklist',
                'meta_description_fr' => 'Guide interactif pour suivre une facture jusqu’au paiement dans WhatsAppBizAI.',
                'meta_description_en' => 'Interactive guide for tracking an invoice through payment in WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(7),
                'sort_order' => 3,
                'views' => 104,
                'difficulty' => 'intermediate',
                'reading_minutes' => 5,
                'steps' => [
                    ['title_fr' => 'Confirmer la réception', 'title_en' => 'Confirm receipt', 'description_fr' => 'Assurez-vous que le client a bien reçu le document.', 'description_en' => 'Make sure the client actually received the invoice.', 'icon' => '📨'],
                    ['title_fr' => 'Rappeler l’échéance', 'title_en' => 'Restate the due date', 'description_fr' => 'Mentionnez clairement la date prévue de paiement.', 'description_en' => 'State the expected payment date clearly.', 'icon' => '⏳'],
                    ['title_fr' => 'Relancer sans agressivité', 'title_en' => 'Follow up without sounding aggressive', 'description_fr' => 'Utilisez un ton ferme mais professionnel.', 'description_en' => 'Use a firm but professional tone.', 'icon' => '🤝'],
                    ['title_fr' => 'Clôturer et archiver', 'title_en' => 'Close and archive', 'description_fr' => 'Une fois payé, mettez le statut à jour et conservez la trace.', 'description_en' => 'Once paid, update the status and keep the record.', 'icon' => '✅'],
                ],
            ],
            [
                'help_category_id' => $categoryIds['automation-ai'],
                'slug' => 'using-ai-assistant-safely',
                'type' => 'article',
                'title_fr' => 'Utiliser l’assistant IA sans perdre votre voix commerciale',
                'title_en' => 'Use the AI assistant without losing your sales voice',
                'excerpt_fr' => 'Comment profiter de la vitesse de l’IA tout en gardant des réponses crédibles, humaines et alignées avec votre marque.',
                'excerpt_en' => 'How to benefit from AI speed while keeping responses credible, human, and aligned with your brand.',
                'content_fr' => <<<'HTML'
<h2>L’IA doit accélérer, pas déshumaniser</h2>
<p>L’assistant IA est excellent pour proposer un premier jet, reformuler un message, synthétiser une demande ou suggérer une relance. En revanche, il fonctionne mieux quand vous gardez la supervision du ton et du contexte.</p>

<h2>Ce que l’IA fait bien</h2>
<ul>
<li>Résumer des échanges longs.</li>
<li>Rédiger des relances courtes.</li>
<li>Adapter le niveau de formalité.</li>
<li>Structurer une proposition commerciale.</li>
</ul>

<h2>Ce que vous devez relire</h2>
<ul>
<li>Les promesses de délai.</li>
<li>Les montants ou conditions commerciales.</li>
<li>Les formulations trop génériques.</li>
<li>Les réponses envoyées à un client sensible ou important.</li>
</ul>

<h2>La bonne méthode</h2>
<p>Considérez l’IA comme un copilote rédactionnel. Laissez-la vous faire gagner du temps, mais gardez toujours la décision finale sur le message envoyé.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>AI should speed you up, not flatten your personality</h2>
<p>The AI assistant is great for first drafts, rewriting messages, summarizing requests, or suggesting follow-ups. It works best when you still supervise tone and context.</p>

<h2>What AI does well</h2>
<ul>
<li>Summarizing long exchanges.</li>
<li>Drafting short follow-ups.</li>
<li>Adapting tone and formality.</li>
<li>Structuring a commercial proposal.</li>
</ul>

<h2>What you should always review</h2>
<ul>
<li>Deadline promises.</li>
<li>Prices or commercial terms.</li>
<li>Overly generic wording.</li>
<li>Responses going to sensitive or high-value clients.</li>
</ul>

<h2>The right approach</h2>
<p>Treat AI as a writing copilot. Let it save time, but keep the final decision on what gets sent.</p>
HTML,
                'meta_title_fr' => 'Bien utiliser l’IA dans WhatsAppBizAI',
                'meta_title_en' => 'How to use AI well in WhatsAppBizAI',
                'meta_description_fr' => 'Conseils pour exploiter l’assistant IA de WhatsAppBizAI tout en gardant un ton humain et crédible.',
                'meta_description_en' => 'Tips for using the WhatsAppBizAI AI assistant while keeping a human and credible tone.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(6),
                'sort_order' => 1,
                'views' => 143,
                'difficulty' => 'intermediate',
                'reading_minutes' => 5,
                'steps' => null,
            ],
            [
                'help_category_id' => $categoryIds['automation-ai'],
                'slug' => 'create-ai-follow-up-message',
                'type' => 'tutorial',
                'title_fr' => 'Créer une relance assistée par IA qui donne envie de répondre',
                'title_en' => 'Create an AI-assisted follow-up that gets replies',
                'excerpt_fr' => 'Un pas à pas pour produire des relances courtes, utiles et moins robotiques.',
                'excerpt_en' => 'A walkthrough for creating short, useful follow-ups that feel less robotic.',
                'content_fr' => <<<'HTML'
<h2>Le problème des mauvaises relances IA</h2>
<p>Les relances générées automatiquement peuvent vite paraître froides, répétitives ou insistantes. Pour obtenir de bons résultats, il faut cadrer la demande et relire le résultat.</p>

<h2>Le bon objectif</h2>
<p>Une bonne relance ne répète pas simplement “je reviens vers vous”. Elle rappelle le contexte, réduit la friction et appelle une réponse simple.</p>

<h2>Exemple de logique</h2>
<ul>
<li>Rappeler la dernière interaction.</li>
<li>Montrer que vous comprenez le besoin.</li>
<li>Proposer une prochaine étape facile.</li>
<li>Rester bref.</li>
</ul>
HTML,
                'content_en' => <<<'HTML'
<h2>The problem with weak AI follow-ups</h2>
<p>Automatically generated follow-ups can quickly sound cold, repetitive, or pushy. To get good results, you need to frame the prompt well and review the output.</p>

<h2>The right goal</h2>
<p>A strong follow-up does more than say “just checking in.” It reminds the client of the context, reduces friction, and asks for an easy response.</p>

<h2>A useful structure</h2>
<ul>
<li>Reference the last interaction.</li>
<li>Show that you understand the need.</li>
<li>Suggest an easy next step.</li>
<li>Keep it short.</li>
</ul>
HTML,
                'meta_title_fr' => 'Créer une relance IA efficace sur WhatsAppBizAI',
                'meta_title_en' => 'Create an effective AI follow-up in WhatsAppBizAI',
                'meta_description_fr' => 'Tutoriel pour créer des relances IA plus humaines et plus efficaces dans WhatsAppBizAI.',
                'meta_description_en' => 'Tutorial for creating more human and effective AI follow-ups in WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'sort_order' => 2,
                'views' => 121,
                'difficulty' => 'intermediate',
                'reading_minutes' => 5,
                'steps' => [
                    ['title_fr' => 'Choisir la bonne conversation', 'title_en' => 'Choose the right conversation', 'description_fr' => 'Utilisez l’IA sur un prospect réellement pertinent à relancer.', 'description_en' => 'Use AI for a genuinely relevant lead.', 'icon' => '🎯'],
                    ['title_fr' => 'Donner le bon contexte', 'title_en' => 'Provide the right context', 'description_fr' => 'Mentionnez le besoin, la dernière date et l’objectif.', 'description_en' => 'Mention the need, last touchpoint, and objective.', 'icon' => '🧩'],
                    ['title_fr' => 'Relire et simplifier', 'title_en' => 'Review and simplify', 'description_fr' => 'Supprimez les phrases creuses et gardez l’essentiel.', 'description_en' => 'Remove filler and keep only what matters.', 'icon' => '✂️'],
                    ['title_fr' => 'Envoyer au bon moment', 'title_en' => 'Send at the right time', 'description_fr' => 'Une bonne relance dépend aussi du timing.', 'description_en' => 'Timing matters as much as wording.', 'icon' => '⏰'],
                ],
            ],
            [
                'help_category_id' => $categoryIds['automation-ai'],
                'slug' => 'automation-readiness-checklist',
                'type' => 'guide',
                'title_fr' => 'Guide interactif : êtes-vous prêt à automatiser vos ventes ?',
                'title_en' => 'Interactive guide: are you ready to automate your sales?',
                'excerpt_fr' => 'Validez les prérequis avant d’activer davantage d’automatisation dans vos processus commerciaux.',
                'excerpt_en' => 'Validate the prerequisites before adding more automation to your sales process.',
                'content_fr' => <<<'HTML'
<h2>L’automatisation n’est utile que si les bases sont stables</h2>
<p>Automatiser un processus flou ne fait qu’accélérer le désordre. Avant d’aller plus loin, vérifiez que vos données, services, statuts et routines de suivi sont déjà suffisamment propres.</p>

<h2>Ce guide vous aide à évaluer votre maturité</h2>
<p>Si vous cochez toutes les étapes, vous êtes dans une bonne position pour intensifier l’usage de l’IA et des relances automatiques.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>Automation only helps when the basics are stable</h2>
<p>Automating a messy process only speeds up the mess. Before going further, make sure your data, service catalog, statuses, and follow-up routines are already reasonably clean.</p>

<h2>This guide helps you assess readiness</h2>
<p>If you can complete all the steps, you are in a strong position to deepen your use of AI and automated follow-ups.</p>
HTML,
                'meta_title_fr' => 'Checklist pour automatiser ses ventes',
                'meta_title_en' => 'Sales automation readiness checklist',
                'meta_description_fr' => 'Guide interactif pour vérifier si votre activité est prête à automatiser davantage les ventes avec WhatsAppBizAI.',
                'meta_description_en' => 'Interactive guide to assess whether your business is ready for deeper sales automation with WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(4),
                'sort_order' => 3,
                'views' => 97,
                'difficulty' => 'advanced',
                'reading_minutes' => 6,
                'steps' => [
                    ['title_fr' => 'Vos contacts sont-ils propres ?', 'title_en' => 'Are your contacts clean?', 'description_fr' => 'Vérifiez la qualité de vos données CRM.', 'description_en' => 'Check the quality of your CRM data.', 'icon' => '🧼'],
                    ['title_fr' => 'Vos services sont-ils standardisés ?', 'title_en' => 'Are your services standardized?', 'description_fr' => 'Des offres claires rendent l’automatisation plus fiable.', 'description_en' => 'Clear offers make automation more reliable.', 'icon' => '📦'],
                    ['title_fr' => 'Vos statuts commerciaux sont-ils utilisés ?', 'title_en' => 'Are your sales statuses actually used?', 'description_fr' => 'Un pipeline réel vaut mieux qu’un pipeline théorique.', 'description_en' => 'A real pipeline is better than a theoretical one.', 'icon' => '📊'],
                    ['title_fr' => 'Relisez-vous les messages sensibles ?', 'title_en' => 'Do you review sensitive messages?', 'description_fr' => 'Gardez une boucle humaine là où c’est critique.', 'description_en' => 'Keep a human checkpoint where it matters.', 'icon' => '🛡️'],
                ],
            ],
        ];

        foreach ($articles as $articleData) {
            HelpArticle::updateOrCreate(
                ['slug' => $articleData['slug']],
                $articleData
            );
        }
    }
}
