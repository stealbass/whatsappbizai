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
                'description_fr' => 'Inscrivez-vous, configurez votre entreprise et lancez-vous en quelques minutes.',
                'description_en' => 'Sign up, configure your business, and get started in minutes.',
            ],
            [
                'slug' => 'crm-contacts',
                'icon' => '👥',
                'color' => '#8b5cf6',
                'sort_order' => 2,
                'is_active' => true,
                'name_fr' => 'CRM & Contacts',
                'name_en' => 'CRM & Contacts',
                'description_fr' => 'Ajoutez, importez et gérez vos contacts. Comprenez les statuts et le pipeline commercial.',
                'description_en' => 'Add, import, and manage your contacts. Understand statuses and the sales pipeline.',
            ],
            [
                'slug' => 'documents',
                'icon' => '💼',
                'color' => '#14b8a6',
                'sort_order' => 3,
                'is_active' => true,
                'name_fr' => 'Devis, Factures & Paiements',
                'name_en' => 'Quotes, Invoices & Payments',
                'description_fr' => 'Créez des devis, générez des factures PDF et envoyez-les par WhatsApp ou email.',
                'description_en' => 'Create quotes, generate PDF invoices, and send them via WhatsApp or email.',
            ],
            [
                'slug' => 'automation-ai',
                'icon' => '🤖',
                'color' => '#f59e0b',
                'sort_order' => 4,
                'is_active' => true,
                'name_fr' => 'Automatisation & IA',
                'name_en' => 'Automation & AI',
                'description_fr' => 'Assistant IA, relances automatiques, broadcast et campagnes de rétention.',
                'description_en' => 'AI assistant, automated follow-ups, broadcasts, and retention campaigns.',
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
            // ═══════════════════════════════════════════════════════════
            // CATEGORY: Getting Started
            // ═══════════════════════════════════════════════════════════
            [
                'help_category_id' => $categoryIds['getting-started'],
                'slug' => 'what-is-whatsappbizai',
                'type' => 'article',
                'title_fr' => 'Qu\'est-ce que WhatsAppBizAI ?',
                'title_en' => 'What is WhatsAppBizAI?',
                'excerpt_fr' => 'Vue d\'ensemble complète : CRM, devis, factures, IA et messages WhatsApp pour les PME.',
                'excerpt_en' => 'Complete overview: CRM, quotes, invoices, AI, and WhatsApp messaging for SMEs.',
                'content_fr' => <<<'HTML'
<h2>Une plateforme tout-en-un pour les PME</h2>
<p>WhatsAppBizAI est un espace de travail SaaS qui combine CRM, facturation, automatisation IA et envoi de messages WhatsApp dans une seule interface. Vous gérez vos contacts, créez des devis et factures, relancez vos clients et utilisez l\'IA pour rédiger vos messages — sans quitter l\'application.</p>

<h2>Les modules principaux</h2>
<ul>
<li><strong>Contacts</strong> — Créez et gérez vos prospects et clients avec statut (prospect, client, inactif), historique des paiements et totaux facturés. Importez vos contacts depuis un fichier CSV.</li>
<li><strong>Conversations IA</strong> — Vos échanges WhatsApp sont synchronisés et l\'IA peut répondre automatiquement selon votre prompt personnalisé.</li>
<li><strong>Devis</strong> — Créez des devis professionnels, envoyez-les par WhatsApp ou email, et convertissez-les en facture en un clic.</li>
<li><strong>Factures</strong> — Générez des factures avec votre logo, suivez les paiements, relancez vos clients et téléchargez les PDF.</li>
<li><strong>Services</strong> — Définissez vos prestations avec prix et unité pour accélérer la création de documents.</li>
<li><strong>Broadcast</strong> — Envoyez des messages en masse à vos contacts WhatsApp.</li>
<li><strong>Rétention</strong> — Lancez des campagnes ciblées (fidélisation, relance, upsell, parrainage) avec assistant IA.</li>
<li><strong>Test Chat IA</strong> — Simulez une conversation WhatsApp pour tester les réponses de l\'IA avant de connecter un vrai compte.</li>
</ul>

<h2>Pour qui est-ce conçu ?</h2>
<p>Freelances, agences, prestataires de services, commerces, consultants et petites équipes commerciales qui vendent via WhatsApp et ont besoin d\'organiser leur activité sans multiplier les outils.</p>

<h2>Par où commencer ?</h2>
<ol>
<li>Inscrivez-vous avec votre adresse email et créez votre compte.</li>
<li>Configurez votre profil entreprise (nom, pays, devise, logo).</li>
<li>Créez vos services principaux.</li>
<li>Ajoutez quelques contacts ou importez-les depuis un CSV.</li>
<li>Créez un premier devis ou facture.</li>
<li>Testez l\'IA avec le Test Chat IA.</li>
</ol>
HTML,
                'content_en' => <<<'HTML'
<h2>An all-in-one platform for SMEs</h2>
<p>WhatsAppBizAI is a SaaS workspace that combines CRM, invoicing, AI automation, and WhatsApp messaging in a single interface. You manage contacts, create quotes and invoices, follow up with clients, and use AI to draft messages — all without leaving the app.</p>

<h2>Key modules</h2>
<ul>
<li><strong>Contacts</strong> — Create and manage prospects and clients with status (prospect, client, inactive), payment history, and invoiced totals. Import contacts from a CSV file.</li>
<li><strong>AI Conversations</strong> — Your WhatsApp conversations are synced and the AI can respond automatically based on your custom prompt.</li>
<li><strong>Quotes</strong> — Create professional quotes, send them via WhatsApp or email, and convert them to invoices in one click.</li>
<li><strong>Invoices</strong> — Generate invoices with your logo, track payments, follow up with clients, and download PDFs.</li>
<li><strong>Services</strong> — Define your offerings with pricing and unit to speed up document creation.</li>
<li><strong>Broadcast</strong> — Send bulk messages to your WhatsApp contacts.</li>
<li><strong>Retention</strong> — Run targeted campaigns (loyalty, win-back, upsell, referral) with AI assistance.</li>
<li><strong>Test Chat AI</strong> — Simulate a WhatsApp conversation to test AI responses before connecting a real account.</li>
</ul>

<h2>Who is it for?</h2>
<p>Freelancers, agencies, service providers, retail businesses, consultants, and small sales teams who sell via WhatsApp and need to organize their activity without juggling multiple tools.</p>

<h2>Where to start?</h2>
<ol>
<li>Sign up with your email address and create your account.</li>
<li>Configure your business profile (name, country, currency, logo).</li>
<li>Create your main services.</li>
<li>Add a few contacts or import them from a CSV.</li>
<li>Create your first quote or invoice.</li>
<li>Test the AI with Test Chat AI.</li>
</ol>
HTML,
                'meta_title_fr' => 'Qu\'est-ce que WhatsAppBizAI ? CRM, devis, factures et IA pour PME',
                'meta_title_en' => 'What is WhatsAppBizAI? CRM, quotes, invoices, and AI for SMEs',
                'meta_description_fr' => 'Découvrez WhatsAppBizAI : CRM, devis, factures, broadcast WhatsApp et assistant IA pour les PME et freelances.',
                'meta_description_en' => 'Discover WhatsAppBizAI: CRM, quotes, invoices, WhatsApp broadcast, and AI assistant for SMEs and freelancers.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(20),
                'sort_order' => 1,
                'views' => 220,
                'difficulty' => 'beginner',
                'reading_minutes' => 4,
                'steps' => null,
            ],
            [
                'help_category_id' => $categoryIds['getting-started'],
                'slug' => 'configure-your-business-profile',
                'type' => 'tutorial',
                'title_fr' => 'Configurer votre profil entreprise',
                'title_en' => 'Configure your business profile',
                'excerpt_fr' => 'Tutoriel complet : nom, pays, devise, logo, fuseau horaire — tout pour un profil professionnel.',
                'excerpt_en' => 'Complete tutorial: name, country, currency, logo, timezone — everything for a professional profile.',
                'content_fr' => <<<'HTML'
<h2>Pourquoi cette configuration est importante</h2>
<p>Votre profil entreprise alimente les informations affichées sur vos devis, factures et PDF. Un nom, une adresse et un logo bien configurés donnent un aspect professionnel à tous vos documents.</p>

<h2>Les champs du formulaire</h2>
<ul>
<li><strong>Nom de l\'entreprise</strong> — Nom commercial affiché sur tous les documents.</li>
<li><strong>Propriétaire</strong> — Nom du responsable ou signataire.</li>
<li><strong>Email</strong> — Adresse de contact principale.</li>
<li><strong>Téléphone</strong> — Numéro professionnel.</li>
<li><strong>Adresse, Ville</strong> — Adresse complète pour vos factures.</li>
<li><strong>Pays</strong> — Dropdown de 54 pays africains et internationaux. Sélectionnez votre pays et la devise + fuseau horaire se mettent à jour automatiquement.</li>
<li><strong>Devise</strong> — 30 devises disponibles (XAF, XOF, EUR, USD, etc.).</li>
<li><strong>Fuseau horaire</strong> — 51 fuseaux horaires avec labels lisibles.</li>
<li><strong>Logo</strong> — Uploadez votre logo (PNG, JPG, SVG — max 2 Mo). Il apparaîtra sur vos factures et devis PDF.</li>
<li><strong>Préfixe factures / devis</strong> — Personnalisez les numéros (ex: FAC-0001, DEV-0001).</li>
</ul>

<h2>Où trouver ces paramètres ?</h2>
<p>Dans votre espace client, allez dans <strong>Paramètres → Mon entreprise</strong>. Modifiez les champs et cliquez sur Enregistrer.</p>

<h2>Vérification</h2>
<p>Créez un devis ou une facture test pour vérifier que les informations s\'affichent correctement dans le PDF généré.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>Why this setup matters</h2>
<p>Your business profile powers the information displayed on your quotes, invoices, and PDFs. A properly configured name, address, and logo give a professional look to all your documents.</p>

<h2>Form fields</h2>
<ul>
<li><strong>Business name</strong> — Trade name shown on all documents.</li>
<li><strong>Owner</strong> — Name of the responsible person or signatory.</li>
<li><strong>Email</strong> — Main contact address.</li>
<li><strong>Phone</strong> — Professional phone number.</li>
<li><strong>Address, City</strong> — Full address for your invoices.</li>
<li><strong>Country</strong> — Dropdown of 54 African and international countries. Select your country and the currency + timezone update automatically.</li>
<li><strong>Currency</strong> — 30 currencies available (XAF, XOF, EUR, USD, etc.).</li>
<li><strong>Timezone</strong> — 51 timezones with readable labels.</li>
<li><strong>Logo</strong> — Upload your logo (PNG, JPG, SVG — max 2 MB). It will appear on your invoice and quote PDFs.</li>
<li><strong>Invoice / Quote prefix</strong> — Customize numbering (e.g., INV-0001, QUO-0001).</li>
</ul>

<h2>Where to find these settings?</h2>
<p>In your client space, go to <strong>Settings → My Business</strong>. Edit the fields and click Save.</p>

<h2>Verification</h2>
<p>Create a test quote or invoice to verify that the information appears correctly in the generated PDF.</p>
HTML,
                'meta_title_fr' => 'Configurer son profil entreprise sur WhatsAppBizAI',
                'meta_title_en' => 'How to configure your business profile in WhatsAppBizAI',
                'meta_description_fr' => 'Tutoriel pour configurer le profil entreprise, la devise, le logo et les paramètres dans WhatsAppBizAI.',
                'meta_description_en' => 'Tutorial for configuring your business profile, currency, logo, and settings in WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(18),
                'sort_order' => 2,
                'views' => 180,
                'difficulty' => 'beginner',
                'reading_minutes' => 5,
                'steps' => [
                    [
                        'title_fr' => 'Ouvrir les paramètres entreprise',
                        'title_en' => 'Open business settings',
                        'description_fr' => 'Dans votre espace client, cliquez sur "Mon entreprise" dans le menu latéral.',
                        'description_en' => 'In your client space, click "My Business" in the sidebar.',
                        'icon' => '⚙️',
                    ],
                    [
                        'title_fr' => 'Renseigner les informations de base',
                        'title_en' => 'Fill in basic information',
                        'description_fr' => 'Ajoutez le nom, le propriétaire, l\'email et le téléphone.',
                        'description_en' => 'Add the business name, owner, email, and phone number.',
                        'icon' => '🏢',
                    ],
                    [
                        'title_fr' => 'Sélectionner le pays et la devise',
                        'title_en' => 'Select country and currency',
                        'description_fr' => 'Choisissez votre pays — la devise et le fuseau horaire se mettent à jour automatiquement.',
                        'description_en' => 'Choose your country — currency and timezone update automatically.',
                        'icon' => '💱',
                    ],
                    [
                        'title_fr' => 'Ajouter le logo',
                        'title_en' => 'Upload the logo',
                        'description_fr' => 'Uploadez votre logo pour qu\'il apparaisse sur les factures et devis.',
                        'description_en' => 'Upload your logo so it appears on invoices and quotes.',
                        'icon' => '🎨',
                    ],
                    [
                        'title_fr' => 'Enregistrer et vérifier',
                        'title_en' => 'Save and verify',
                        'description_fr' => 'Créez un devis test pour contrôler le rendu du PDF.',
                        'description_en' => 'Create a test quote to check the PDF output.',
                        'icon' => '✅',
                    ],
                ],
            ],
            [
                'help_category_id' => $categoryIds['getting-started'],
                'slug' => 'first-day-setup-checklist',
                'type' => 'guide',
                'title_fr' => 'Checklist : réussir votre premier jour',
                'title_en' => 'Checklist: make your first day a success',
                'excerpt_fr' => 'Checklist complète pour configurer votre compte, ajouter vos services et créer votre premier devis en une heure.',
                'excerpt_en' => 'Complete checklist to set up your account, add your services, and create your first quote in one hour.',
                'content_fr' => <<<'HTML'
<h2>Objectif</h2>
<p>Cette checklist vous passe d\'un compte fraîchement créé à un espace prêt à l\'usage. Suivez les étapes dans l\'ordre.</p>

<h2>Avant de commencer</h2>
<p>Ayez sous la main : le nom de votre entreprise, votre numéro WhatsApp professionnel, une adresse email, et les tarifs de vos services principaux.</p>

<h2>Étape 1 : Profil entreprise (5 min)</h2>
<p>Allez dans <strong>Paramètres → Mon entreprise</strong>. Remplissez le nom, sélectionnez votre pays (la devise se met à jour automatiquement), ajoutez votre logo et enregistrez.</p>

<h2>Étape 2 : Services (5 min)</h2>
<p>Allez dans <strong>Services</strong> et créez au moins 2-3 de vos prestations principales avec prix et unité. Ces services seront utilisés dans vos devis et factures.</p>

<h2>Étape 3 : Contacts (5 min)</h2>
<p>Allez dans <strong>Contacts</strong> et ajoutez 2-3 contacts test. Vous pouvez aussi importer un fichier CSV si vous avez une liste existante.</p>

<h2>Étape 4 : Premier devis (10 min)</h2>
<p>Allez dans <strong>Devis → Nouveau devis</strong>. Sélectionnez un contact, ajoutez vos services, et créez le devis. Téléchargez le PDF pour vérifier le rendu.</p>

<h2>Étape 5 : Envoyer le devis (5 min)</h2>
<p>Depuis la page de détail du devis, cliquez sur "Envoyer WhatsApp" ou "Envoyer par email" pour tester l\'envoi.</p>

<h2>Étape 6 : Test Chat IA (5 min)</h2>
<p>Ouvrez le <strong>Test Chat IA</strong> depuis la barre latérale. Posez des questions typiques de vos clients pour vérifier que l\'IA répond correctement.</p>

<h2>Bonus : Connecter WhatsApp</h2>
<p>Allez dans <strong>Paramètres → WhatsApp</strong> et connectez votre compte WhatsApp Business via le portail Meta. En attendant, le mode sandbox est activé pour tester les envois.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>Goal</h2>
<p>This checklist takes you from a freshly created account to a workspace ready for use. Follow the steps in order.</p>

<h2>Before you start</h2>
<p>Have ready: your business name, your professional WhatsApp number, an email address, and the pricing for your main services.</p>

<h2>Step 1: Business profile (5 min)</h2>
<p>Go to <strong>Settings → My Business</strong>. Fill in the name, select your country (currency updates automatically), add your logo, and save.</p>

<h2>Step 2: Services (5 min)</h2>
<p>Go to <strong>Services</strong> and create at least 2-3 of your main offerings with pricing and unit. These services will be used in your quotes and invoices.</p>

<h2>Step 3: Contacts (5 min)</h2>
<p>Go to <strong>Contacts</strong> and add 2-3 test contacts. You can also import a CSV file if you have an existing list.</p>

<h2>Step 4: First quote (10 min)</h2>
<p>Go to <strong>Quotes → New Quote</strong>. Select a contact, add your services, and create the quote. Download the PDF to check the output.</p>

<h2>Step 5: Send the quote (5 min)</h2>
<p>From the quote detail page, click "Send via WhatsApp" or "Send via Email" to test sending.</p>

<h2>Step 6: Test Chat AI (5 min)</h2>
<p>Open <strong>Test Chat AI</strong> from the sidebar. Ask typical customer questions to verify the AI responds correctly.</p>

<h2>Bonus: Connect WhatsApp</h2>
<p>Go to <strong>Settings → WhatsApp</strong> and connect your WhatsApp Business account via the Meta portal. Meanwhile, sandbox mode is enabled for testing.</p>
HTML,
                'meta_title_fr' => 'Checklist premier jour WhatsAppBizAI',
                'meta_title_en' => 'WhatsAppBizAI first-day setup checklist',
                'meta_description_fr' => 'Guide complet pour configurer votre compte et démarrer avec WhatsAppBizAI en moins d\'une heure.',
                'meta_description_en' => 'Complete guide to set up your account and get started with WhatsAppBizAI in under an hour.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(16),
                'sort_order' => 3,
                'views' => 140,
                'difficulty' => 'beginner',
                'reading_minutes' => 5,
                'steps' => [
                    ['title_fr' => 'Configurer le profil entreprise', 'title_en' => 'Set up the business profile', 'description_fr' => 'Nom, email, téléphone, pays, devise et logo.', 'description_en' => 'Name, email, phone, country, currency, and logo.', 'icon' => '🏁'],
                    ['title_fr' => 'Créer vos services', 'title_en' => 'Create your services', 'description_fr' => 'Ajoutez vos prestations avec prix et unité.', 'description_en' => 'Add your offerings with pricing and unit.', 'icon' => '🧩'],
                    ['title_fr' => 'Ajouter des contacts', 'title_en' => 'Add contacts', 'description_fr' => 'Créez au moins 2-3 contacts ou importez un CSV.', 'description_en' => 'Create at least 2-3 contacts or import a CSV.', 'icon' => '👥'],
                    ['title_fr' => 'Créer un devis', 'title_en' => 'Create a quote', 'description_fr' => 'Liez-le à un contact et ajoutez des services.', 'description_en' => 'Link it to a contact and add services.', 'icon' => '🧾'],
                    ['title_fr' => 'Envoyer le devis', 'title_en' => 'Send the quote', 'description_fr' => 'Envoyez par WhatsApp ou email depuis la page de détail.', 'description_en' => 'Send via WhatsApp or email from the detail page.', 'icon' => '📤'],
                    ['title_fr' => 'Tester le Test Chat IA', 'title_en' => 'Test the AI Test Chat', 'description_fr' => 'Simulez une conversation pour voir les réponses de l\'IA.', 'description_en' => 'Simulate a conversation to see AI responses.', 'icon' => '🤖'],
                ],
            ],

            // ═══════════════════════════════════════════════════════════
            // CATEGORY: CRM & Contacts
            // ═══════════════════════════════════════════════════════════
            [
                'help_category_id' => $categoryIds['crm-contacts'],
                'slug' => 'import-contacts-csv',
                'type' => 'tutorial',
                'title_fr' => 'Importer vos contacts depuis un fichier CSV',
                'title_en' => 'Import your contacts from a CSV file',
                'excerpt_fr' => 'Comment importer en masse vos contacts existants depuis Excel, Google Sheets ou un fichier CSV.',
                'excerpt_en' => 'How to bulk import your existing contacts from Excel, Google Sheets, or a CSV file.',
                'content_fr' => <<<'HTML'
<h2>Pourquoi importer un CSV ?</h2>
<p>Si vous avez déjà une liste de contacts dans Excel, Google Sheets ou un autre outil, l\'import CSV vous permet de tous les ajouter en une seule action au lieu de les saisir un par un.</p>

<h2>Étape 1 : Préparer le fichier</h2>
<p>Votre fichier CSV doit avoir :</p>
<ul>
<li>Une <strong>première ligne d\'en-tête</strong> avec les noms de colonnes.</li>
<li>Un <strong>séparateur</strong> point-virgule (;) ou virgule (,).</li>
<li>La colonne <strong>whatsapp_number</strong> (ou équivalent) est obligatoire.</li>
</ul>

<h2>Colonnes acceptées</h2>
<p>Le système reconnaît automatiquement les alias de colonnes (insensible à la casse) :</p>
<ul>
<li><strong>Nom</strong> : name, nom, full_name</li>
<li><strong>WhatsApp</strong> : whatsapp, whatsapp_number, phone, telephone, tel, numéro</li>
<li><strong>Email</strong> : email, e-mail, courriel</li>
<li><strong>Entreprise</strong> : company, entreprise, société</li>
<li><strong>Statut</strong> : status, statut (prospect, client, inactif)</li>
<li><strong>Notes</strong> : notes, remarques</li>
</ul>

<h2>Étape 2 : Télécharger le modèle</h2>
<p>Depuis la page d\'import (<strong>Contacts → Importer CSV</strong>), cliquez sur "Télécharger le modèle" pour obtenir un fichier CSV exemple avec le bon format.</p>

<h2>Étape 3 : Importer</h2>
<ol>
<li>Cliquez sur "Importer CSV" depuis la page Contacts.</li>
<li>Sélectionnez votre fichier CSV.</li>
<li>Cliquez sur "Importer les contacts".</li>
<li>Le système affiche le résultat : nombre de contacts importés, ignorés et erreurs éventuelles.</li>
</ol>

<h2>Comportement de l\'import</h2>
<ul>
<li><strong>Création</strong> — Les nouveaux contacts sont créés avec le statut par défaut "prospect".</li>
<li><strong>Mise à jour</strong> — Si un contact avec le même numéro WhatsApp existe déjà, il est mis à jour.</li>
<li><strong>Numéros normalisés</strong> — Les numéros sont nettoyés et formatés en format international (+...).</li>
<li><strong>Statuts</strong> — Les valeurs acceptées sont : client, customer, active → client ; inactif, inactive → inactif ; sinon → prospect.</li>
</ul>
HTML,
                'content_en' => <<<'HTML'
<h2>Why import a CSV?</h2>
<p>If you already have a contact list in Excel, Google Sheets, or another tool, CSV import lets you add them all in one action instead of entering them one by one.</p>

<h2>Step 1: Prepare the file</h2>
<p>Your CSV file must have:</p>
<ul>
<li>A <strong>header row</strong> with column names.</li>
<li>A <strong>delimiter</strong> of semicolon (;) or comma (,).</li>
<li>The <strong>whatsapp_number</strong> column (or equivalent) is required.</li>
</ul>

<h2>Accepted columns</h2>
<p>The system automatically recognizes column aliases (case-insensitive):</p>
<ul>
<li><strong>Name</strong>: name, nom, full_name</li>
<li><strong>WhatsApp</strong>: whatsapp, whatsapp_number, phone, telephone, tel, numéro</li>
<li><strong>Email</strong>: email, e-mail, courriel</li>
<li><strong>Company</strong>: company, entreprise, société</li>
<li><strong>Status</strong>: status, statut (prospect, client, inactif)</li>
<li><strong>Notes</strong>: notes, remarques</li>
</ul>

<h2>Step 2: Download the template</h2>
<p>From the import page (<strong>Contacts → CSV Import</strong>), click "Download template" to get a sample CSV file with the correct format.</p>

<h2>Step 3: Import</h2>
<ol>
<li>Click "CSV Import" from the Contacts page.</li>
<li>Select your CSV file.</li>
<li>Click "Import contacts".</li>
<li>The system shows results: number of contacts imported, skipped, and any errors.</li>
</ol>

<h2>Import behavior</h2>
<ul>
<li><strong>Creation</strong> — New contacts are created with the default "prospect" status.</li>
<li><strong>Update</strong> — If a contact with the same WhatsApp number already exists, it is updated.</li>
<li><strong>Number normalization</strong> — Numbers are cleaned and formatted in international format (+...).</li>
<li><strong>Statuses</strong> — Accepted values: client, customer, active → client; inactif, inactive → inactif; otherwise → prospect.</li>
</ul>
HTML,
                'meta_title_fr' => 'Importer des contacts CSV sur WhatsAppBizAI',
                'meta_title_en' => 'Import CSV contacts in WhatsAppBizAI',
                'meta_description_fr' => 'Tutoriel pour importer vos contacts depuis un fichier CSV dans WhatsAppBizAI.',
                'meta_description_en' => 'Tutorial for importing contacts from a CSV file in WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(17),
                'sort_order' => 1,
                'views' => 165,
                'difficulty' => 'beginner',
                'reading_minutes' => 4,
                'steps' => [
                    ['title_fr' => 'Préparer le fichier CSV', 'title_en' => 'Prepare the CSV file', 'description_fr' => 'Utilisez le modèle téléchargé ou créez votre fichier avec les bonnes colonnes.', 'description_en' => 'Use the downloaded template or create your file with the right columns.', 'icon' => '📄'],
                    ['title_fr' => 'Ouvrir la page d\'import', 'title_en' => 'Open the import page', 'description_fr' => 'Cliquez sur "Importer CSV" depuis la page Contacts.', 'description_en' => 'Click "CSV Import" from the Contacts page.', 'icon' => '📥'],
                    ['title_fr' => 'Sélectionner le fichier', 'title_en' => 'Select the file', 'description_fr' => 'Choisissez votre fichier CSV et cliquez sur Importer.', 'description_en' => 'Choose your CSV file and click Import.', 'icon' => '📁'],
                    ['title_fr' => 'Vérifier les résultats', 'title_en' => 'Check results', 'description_fr' => 'Contrôlez le nombre de contacts importés et les éventuelles erreurs.', 'description_en' => 'Review the number of imported contacts and any errors.', 'icon' => '✅'],
                ],
            ],
            [
                'help_category_id' => $categoryIds['crm-contacts'],
                'slug' => 'manage-contact-statuses',
                'type' => 'article',
                'title_fr' => 'Comprendre les statuts de contacts : prospect, client, inactif',
                'title_en' => 'Understanding contact statuses: prospect, client, inactive',
                'excerpt_fr' => 'Comment les statuts organisent votre pipeline commercial et permettent de cibler vos actions.',
                'excerpt_en' => 'How statuses organize your sales pipeline and enable targeted actions.',
                'content_fr' => <<<'HTML'
<h2>Les trois statuts disponibles</h2>
<p>Chaque contact dans WhatsAppBizAI peut avoir l\'un des trois statuts suivants :</p>
<ul>
<li><strong>Prospect</strong> — La personne a manifesté un intérêt mais n\'a pas encore acheté. C\'est le statut par défaut lors de l\'import CSV.</li>
<li><strong>Client</strong> — Une vente a eu lieu ou une relation commerciale est active. Le système calcule automatiquement les totaux facturés et payés.</li>
<li><strong>Inactif</strong> — Pas d\'échange utile depuis un certain temps.</li>
</ul>

<h2>Pourquoi c\'est utile</h2>
<p>Les statuts vous permettent de filtrer vos contacts et de cibler vos actions :</p>
<ul>
<li>Envoyer un <strong>broadcast</strong> uniquement aux prospects pour les convertir.</li>
<li>Lancer une <strong>campagne de rétention</strong> aux clients inactifs depuis 30+ jours.</li>
<li>Cibler les <strong>clients à forte valeur</strong> (plus de 100 000 XAF facturés) avec une offre upsell.</li>
</ul>

<h2>Où modifier le statut ?</h2>
<p>Allez dans <strong>Contacts</strong>, cliquez sur un contact, et modifiez le champ "Statut" lors de la création ou de la modification.</p>

<h2>Le statut et la rétention</h2>
<p>Le module <strong>Rétention</strong> utilise ces statuts pour cibler automatiquement vos campagnes : clients inactifs, prospects, clients à forte valeur.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>The three available statuses</h2>
<p>Each contact in WhatsAppBizAI can have one of three statuses:</p>
<ul>
<li><strong>Prospect</strong> — The person has shown interest but has not purchased yet. This is the default status on CSV import.</li>
<li><strong>Client</strong> — A sale has occurred or the business relationship is active. The system automatically calculates invoiced and paid totals.</li>
<li><strong>Inactive</strong> — No meaningful exchange for a while.</li>
</ul>

<h2>Why it matters</h2>
<p>Statuses let you filter contacts and target your actions:</p>
<ul>
<li>Send a <strong>broadcast</strong> only to prospects to convert them.</li>
<li>Run a <strong>retention campaign</strong> for clients inactive for 30+ days.</li>
<li>Target <strong>high-value clients</strong> (over 100,000 XAF invoiced) with an upsell offer.</li>
</ul>

<h2>Where to change the status?</h2>
<p>Go to <strong>Contacts</strong>, click on a contact, and edit the "Status" field when creating or editing.</p>

<h2>Status and retention</h2>
<p>The <strong>Retention</strong> module uses these statuses to automatically target your campaigns: inactive clients, prospects, and high-value clients.</p>
HTML,
                'meta_title_fr' => 'Statuts de contacts : prospect, client, inactif sur WhatsAppBizAI',
                'meta_title_en' => 'Contact statuses: prospect, client, inactive in WhatsAppBizAI',
                'meta_description_fr' => 'Comprenez les statuts de contacts et utilisez-les pour organiser votre pipeline commercial.',
                'meta_description_en' => 'Understand contact statuses and use them to organize your sales pipeline.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(15),
                'sort_order' => 2,
                'views' => 125,
                'difficulty' => 'beginner',
                'reading_minutes' => 3,
                'steps' => null,
            ],
            [
                'help_category_id' => $categoryIds['crm-contacts'],
                'slug' => 'broadcast-to-contacts',
                'type' => 'guide',
                'title_fr' => 'Guide : envoyer un broadcast WhatsApp à vos contacts',
                'title_en' => 'Guide: send a WhatsApp broadcast to your contacts',
                'excerpt_fr' => 'Comment utiliser le module Broadcast pour envoyer un message personnalisé à plusieurs contacts.',
                'excerpt_en' => 'How to use the Broadcast module to send a personalized message to multiple contacts.',
                'content_fr' => <<<'HTML'
<h2>Le module Broadcast</h2>
<p>Le module <strong>Broadcast</strong> vous permet d\'envoyer un message WhatsApp à une liste de contacts en une seule action. C\'est idéal pour les annonces, promotions ou informations générales.</p>

<h2>Prérequis</h2>
<ul>
<li>Votre WhatsApp doit être connecté (pas en mode sandbox).</li>
<li>Les contacts doivent avoir un numéro WhatsApp renseigné.</li>
</ul>

<h2>Étapes</h2>
<ol>
<li>Allez dans <strong>Broadcast</strong> depuis le menu latéral.</li>
<li>Sélectionnez les contacts destinataires.</li>
<li>Rédigez votre message. Vous pouvez utiliser des variables : <code>{{nom}}</code>, <code>{{prenom}}</code>, <code>{{entreprise}}</code>.</li>
<li>Cliquez sur Envoyer.</li>
</ol>

<h2>Bonnes pratiques</h2>
<ul>
<li>Personnalisez le message avec le nom du contact.</li>
<li>Envoyez à des créneaux raisonnables (8h-20h).</li>
<li>N\'envoyez pas trop fréquemment pour éviter le spam.</li>
<li>Testez d\'abord avec le Test Chat IA pour vérifier la qualité du message.</li>
</ul>
HTML,
                'content_en' => <<<'HTML'
<h2>The Broadcast module</h2>
<p>The <strong>Broadcast</strong> module lets you send a WhatsApp message to a list of contacts in one action. It is ideal for announcements, promotions, or general information.</p>

<h2>Prerequisites</h2>
<ul>
<li>Your WhatsApp must be connected (not in sandbox mode).</li>
<li>Contacts must have a WhatsApp number filled in.</li>
</ul>

<h2>Steps</h2>
<ol>
<li>Go to <strong>Broadcast</strong> from the sidebar.</li>
<li>Select the recipient contacts.</li>
<li>Write your message. You can use variables: <code>{{nom}}</code>, <code>{{prenom}}</code>, <code>{{entreprise}}</code>.</li>
<li>Click Send.</li>
</ol>

<h2>Best practices</h2>
<ul>
<li>Personalize the message with the contact\'s name.</li>
<li>Send during reasonable hours (8am-8pm).</li>
<li>Don\'t send too frequently to avoid being flagged as spam.</li>
<li>Test with Test Chat AI first to verify message quality.</li>
</ul>
HTML,
                'meta_title_fr' => 'Envoyer un broadcast WhatsApp sur WhatsAppBizAI',
                'meta_title_en' => 'Send a WhatsApp broadcast in WhatsAppBizAI',
                'meta_description_fr' => 'Guide pour envoyer un message broadcast à vos contacts WhatsApp depuis WhatsAppBizAI.',
                'meta_description_en' => 'Guide for sending a broadcast message to your WhatsApp contacts from WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(11),
                'sort_order' => 3,
                'views' => 111,
                'difficulty' => 'beginner',
                'reading_minutes' => 4,
                'steps' => [
                    ['title_fr' => 'Ouvrir le module Broadcast', 'title_en' => 'Open the Broadcast module', 'description_fr' => 'Cliquez sur "Broadcast" dans le menu latéral.', 'description_en' => 'Click "Broadcast" in the sidebar.', 'icon' => '📢'],
                    ['title_fr' => 'Sélectionner les contacts', 'title_en' => 'Select contacts', 'description_fr' => 'Choisissez les destinataires dans la liste.', 'description_en' => 'Choose recipients from the list.', 'icon' => '👥'],
                    ['title_fr' => 'Rédiger le message', 'title_en' => 'Write the message', 'description_fr' => 'Utilisez {{nom}} pour personnaliser.', 'description_en' => 'Use {{nom}} to personalize.', 'icon' => '✍️'],
                    ['title_fr' => 'Envoyer', 'title_en' => 'Send', 'description_fr' => 'Vérifiez et cliquez sur Envoyer.', 'description_en' => 'Review and click Send.', 'icon' => '📤'],
                ],
            ],

            // ═══════════════════════════════════════════════════════════
            // CATEGORY: Quotes, Invoices & Payments
            // ═══════════════════════════════════════════════════════════
            [
                'help_category_id' => $categoryIds['documents'],
                'slug' => 'quote-to-invoice-workflow',
                'type' => 'article',
                'title_fr' => 'Du devis à la facture : le flux commercial complet',
                'title_en' => 'From quote to invoice: the complete sales workflow',
                'excerpt_fr' => 'Le flux complet : créer un devis, l\'envoyer, le convertir en facture et suivre le paiement.',
                'excerpt_en' => 'The complete workflow: create a quote, send it, convert to invoice, and track payment.',
                'content_fr' => <<<'HTML'
<h2>Le flux en 5 étapes</h2>
<ol>
<li><strong>Créer un devis</strong> — Sélectionnez le contact, ajoutez les services, définissez la validité.</li>
<li><strong>Envoyer le devis</strong> — Par WhatsApp (📤 Envoyer WhatsApp) ou par email (✉️ Envoyer par email). Le statut passe à "Envoyé".</li>
<li><strong>Relancer si nécessaire</strong> — Utilisez le bouton "🔔 Relance WhatsApp" pour envoyer un rappel.</li>
<li><strong>Convertir en facture</strong> — Si le devis est accepté, cliquez sur "→ Convertir en facture". Tout est copié automatiquement.</li>
<li><strong>Suivre le paiement</strong> — Sur la facture, cliquez sur "✅ Marquer payée" une fois le règlement reçu.</li>
</ol>

<h2>Les options d\'envoi</h2>
<ul>
<li><strong>WhatsApp</strong> — Génère le PDF et l\'envoie via l\'API WhatsApp Business. Nécessite un contact avec un numéro WhatsApp et un business connecté.</li>
<li><strong>Email</strong> — Génère le PDF et l\'envoie en pièce jointe par email. Nécessite un contact avec une adresse email.</li>
<li><strong>Télécharger PDF</strong> — Télécharge le fichier sur votre appareil pour envoi manuel.</li>
</ul>

<h2>Numérotation automatique</h2>
<p>Les numéros sont générés automatiquement : <code>FAC-2026-0001</code> pour les factures, <code>DEV-2026-0001</code> pour les devis. Vous pouvez personnaliser les préfixes dans les paramètres entreprise.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>The 5-step workflow</h2>
<ol>
<li><strong>Create a quote</strong> — Select the contact, add services, set the validity period.</li>
<li><strong>Send the quote</strong> — Via WhatsApp (📤 Send WhatsApp) or email (✉️ Send via Email). Status changes to "Sent".</li>
<li><strong>Follow up if needed</strong> — Use the "🔔 WhatsApp Reminder" button to send a reminder.</li>
<li><strong>Convert to invoice</strong> — If the quote is accepted, click "→ Convert to Invoice". Everything is copied automatically.</li>
<li><strong>Track payment</strong> — On the invoice, click "✅ Mark as Paid" once payment is received.</li>
</ol>

<h2>Sending options</h2>
<ul>
<li><strong>WhatsApp</strong> — Generates the PDF and sends it via the WhatsApp Business API. Requires a contact with a WhatsApp number and a connected business.</li>
<li><strong>Email</strong> — Generates the PDF and sends it as an email attachment. Requires a contact with an email address.</li>
<li><strong>Download PDF</strong> — Downloads the file to your device for manual sending.</li>
</ul>

<h2>Automatic numbering</h2>
<p>Numbers are generated automatically: <code>INV-2026-0001</code> for invoices, <code>QUO-2026-0001</code> for quotes. You can customize prefixes in business settings.</p>
HTML,
                'meta_title_fr' => 'Workflow devis → facture sur WhatsAppBizAI',
                'meta_title_en' => 'Quote → invoice workflow in WhatsAppBizAI',
                'meta_description_fr' => 'Créez un devis, envoyez-le par WhatsApp ou email, convertissez-le en facture et suivez le paiement.',
                'meta_description_en' => 'Create a quote, send it via WhatsApp or email, convert it to an invoice, and track payment.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(10),
                'sort_order' => 1,
                'views' => 118,
                'difficulty' => 'beginner',
                'reading_minutes' => 4,
                'steps' => null,
            ],
            [
                'help_category_id' => $categoryIds['documents'],
                'slug' => 'create-and-send-first-quote',
                'type' => 'tutorial',
                'title_fr' => 'Créer et envoyer votre premier devis',
                'title_en' => 'Create and send your first quote',
                'excerpt_fr' => 'Tutoriel pas à pas pour créer un devis avec votre logo, l\'envoyer et vérifier le PDF.',
                'excerpt_en' => 'Step-by-step tutorial to create a quote with your logo, send it, and verify the PDF.',
                'content_fr' => <<<'HTML'
<h2>Créer un devis</h2>
<ol>
<li>Allez dans <strong>Devis → Nouveau devis</strong>.</li>
<li>Sélectionnez un contact existant (ou créez-en un d\'abord).</li>
<li>Ajoutez les lignes de services : description, quantité, prix unitaire.</li>
<li>Optionnel : ajoutez une TVA (%) et une remise.</li>
<li>Définissez la date d\'émission et la date de validité.</li>
<li>Cliquez sur "Créer le devis".</li>
</ol>

<h2>Envoyer le devis</h2>
<p>Sur la page de détail du devis, vous avez trois options :</p>
<ul>
<li><strong>📲 Envoyer WhatsApp</strong> — Le PDF est généré et envoyé via WhatsApp. Le contact doit avoir un numéro WhatsApp.</li>
<li><strong>✉️ Envoyer par email</strong> — Le PDF est généré et envoyé en pièce jointe. Le contact doit avoir une adresse email.</li>
<li><strong>📥 Télécharger PDF</strong> — Vous téléchargez le fichier pour l\'envoyer vous-même.</li>
</ul>

<h2>Convertir en facture</h2>
<p>Si le client accepte le devis, cliquez sur <strong>"→ Convertir en facture"</strong>. Toutes les lignes, montants et informations sont copiés automatiquement dans une nouvelle facture.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>Creating a quote</h2>
<ol>
<li>Go to <strong>Quotes → New Quote</strong>.</li>
<li>Select an existing contact (or create one first).</li>
<li>Add service lines: description, quantity, unit price.</li>
<li>Optional: add tax (%) and discount.</li>
<li>Set the issue date and validity date.</li>
<li>Click "Create Quote".</li>
</ol>

<h2>Sending the quote</h2>
<p>On the quote detail page, you have three options:</p>
<ul>
<li><strong>📲 Send via WhatsApp</strong> — The PDF is generated and sent via WhatsApp. The contact must have a WhatsApp number.</li>
<li><strong>✉️ Send via Email</strong> — The PDF is generated and sent as an attachment. The contact must have an email address.</li>
<li><strong>📥 Download PDF</strong> — You download the file to send it yourself.</li>
</ul>

<h2>Converting to invoice</h2>
<p>If the client accepts the quote, click <strong>"→ Convert to Invoice"</strong>. All lines, amounts, and information are automatically copied to a new invoice.</p>
HTML,
                'meta_title_fr' => 'Créer un devis sur WhatsAppBizAI — Tutoriel',
                'meta_title_en' => 'Create a quote in WhatsAppBizAI — Tutorial',
                'meta_description_fr' => 'Tutoriel pour créer un devis, l\'envoyer par WhatsApp ou email, et le convertir en facture.',
                'meta_description_en' => 'Tutorial for creating a quote, sending it via WhatsApp or email, and converting it to an invoice.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(8),
                'sort_order' => 2,
                'views' => 126,
                'difficulty' => 'beginner',
                'reading_minutes' => 5,
                'steps' => [
                    ['title_fr' => 'Ouvrir la page Devis', 'title_en' => 'Open the Quotes page', 'description_fr' => 'Cliquez sur "Devis" dans le menu latéral.', 'description_en' => 'Click "Quotes" in the sidebar.', 'icon' => '🧾'],
                    ['title_fr' => 'Sélectionner le contact', 'title_en' => 'Select the contact', 'description_fr' => 'Choisissez le client ou prospect concerné.', 'description_en' => 'Choose the relevant client or lead.', 'icon' => '👤'],
                    ['title_fr' => 'Ajouter les services', 'title_en' => 'Add services', 'description_fr' => 'Décrivez chaque prestation avec quantité et prix.', 'description_en' => 'Describe each service with quantity and price.', 'icon' => '📋'],
                    ['title_fr' => 'Créer et envoyer', 'title_en' => 'Create and send', 'description_fr' => 'Cliquez sur Créer, puis choisissez WhatsApp ou email.', 'description_en' => 'Click Create, then choose WhatsApp or email.', 'icon' => '📤'],
                ],
            ],
            [
                'help_category_id' => $categoryIds['documents'],
                'slug' => 'invoice-pdf-and-sending',
                'type' => 'tutorial',
                'title_fr' => 'Créer une facture et l\'envoyer par email ou WhatsApp',
                'title_en' => 'Create an invoice and send it via email or WhatsApp',
                'excerpt_fr' => 'Comment créer une facture avec votre logo, générer le PDF et l\'envoyer par email ou WhatsApp.',
                'excerpt_en' => 'How to create an invoice with your logo, generate the PDF, and send it via email or WhatsApp.',
                'content_fr' => <<<'HTML'
<h2>Créer une facture</h2>
<ol>
<li>Allez dans <strong>Factures → Nouvelle facture</strong>.</li>
<li>Sélectionnez un contact.</li>
<li>Ajoutez les lignes de prestation.</li>
<li>Optionnel : TVA, remise, notes (ex: "Payer par OM/MOMO").</li>
<li>Cliquez sur "Créer la facture".</li>
</ol>

<h2>Le PDF de la facture</h2>
<p>Le PDF contient automatiquement :</p>
<ul>
<li>Votre <strong>logo entreprise</strong> (si uploadé dans les paramètres).</li>
<li>Votre nom, adresse, email et téléphone.</li>
<li>Les informations du client (nom, email, WhatsApp).</li>
<li>Le détail des lignes avec quantités et prix.</li>
<li>Les totaux (sous-total, TVA, remise, total).</li>
<li>Le lien "Généré par WhatsAppBizAI" dans le footer.</li>
</ul>

<h2>Envoyer la facture</h2>
<ul>
<li><strong>📲 Envoyer WhatsApp</strong> — Envoie le PDF via WhatsApp et passe le statut à "Envoyé".</li>
<li><strong>✉️ Envoyer par email</strong> — Envoie le PDF en pièce jointe et passe le statut à "Envoyé". Le contact doit avoir une adresse email.</li>
<li><strong>🔔 Relance WhatsApp</strong> — Envoie un message de rappel avec le montant et l\'échéance.</li>
<li><strong>📥 Télécharger PDF</strong> — Télécharge le fichier.</li>
</ul>

<h2>Suivi des paiements</h2>
<p>Quand le client paie, cliquez sur <strong>"✅ Marquer payée"</strong> pour mettre à jour le statut. Le total payé du contact est automatiquement mis à jour.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>Creating an invoice</h2>
<ol>
<li>Go to <strong>Invoices → New Invoice</strong>.</li>
<li>Select a contact.</li>
<li>Add service lines.</li>
<li>Optional: tax, discount, notes (e.g., "Pay via OM/MOMO").</li>
<li>Click "Create Invoice".</li>
</ol>

<h2>The invoice PDF</h2>
<p>The PDF automatically contains:</p>
<ul>
<li>Your <strong>business logo</strong> (if uploaded in settings).</li>
<li>Your name, address, email, and phone.</li>
<li>Client information (name, email, WhatsApp).</li>
<li>Line details with quantities and prices.</li>
<li>Totals (subtotal, tax, discount, total).</li>
<li>The "Generated by WhatsAppBizAI" link in the footer.</li>
</ul>

<h2>Sending the invoice</h2>
<ul>
<li><strong>📲 Send via WhatsApp</strong> — Sends the PDF via WhatsApp and sets status to "Sent".</li>
<li><strong>✉️ Send via Email</strong> — Sends the PDF as an attachment and sets status to "Sent". The contact must have an email address.</li>
<li><strong>🔔 WhatsApp Reminder</strong> — Sends a reminder message with the amount and due date.</li>
<li><strong>📥 Download PDF</strong> — Downloads the file.</li>
</ul>

<h2>Tracking payments</h2>
<p>When the client pays, click <strong>"✅ Mark as Paid"</strong> to update the status. The contact\'s paid total is automatically updated.</p>
HTML,
                'meta_title_fr' => 'Créer et envoyer une facture sur WhatsAppBizAI',
                'meta_title_en' => 'Create and send an invoice in WhatsAppBizAI',
                'meta_description_fr' => 'Tutoriel pour créer une facture avec logo, générer le PDF et l\'envoyer par WhatsApp ou email.',
                'meta_description_en' => 'Tutorial for creating an invoice with logo, generating the PDF, and sending it via WhatsApp or email.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(7),
                'sort_order' => 3,
                'views' => 104,
                'difficulty' => 'beginner',
                'reading_minutes' => 5,
                'steps' => [
                    ['title_fr' => 'Ouvrir la page Factures', 'title_en' => 'Open the Invoices page', 'description_fr' => 'Cliquez sur "Factures" dans le menu.', 'description_en' => 'Click "Invoices" in the menu.', 'icon' => '🧾'],
                    ['title_fr' => 'Créer la facture', 'title_en' => 'Create the invoice', 'description_fr' => 'Sélectionnez le contact et ajoutez les lignes.', 'description_en' => 'Select the contact and add lines.', 'icon' => '➕'],
                    ['title_fr' => 'Vérifier le PDF', 'title_en' => 'Verify the PDF', 'description_fr' => 'Téléchargez le PDF pour vérifier le rendu avec votre logo.', 'description_en' => 'Download the PDF to check the output with your logo.', 'icon' => '📥'],
                    ['title_fr' => 'Envoyer', 'title_en' => 'Send', 'description_fr' => 'Envoyez par WhatsApp ou email depuis la page de détail.', 'description_en' => 'Send via WhatsApp or email from the detail page.', 'icon' => '📤'],
                ],
            ],

            // ═══════════════════════════════════════════════════════════
            // CATEGORY: Automation & AI
            // ═══════════════════════════════════════════════════════════
            [
                'help_category_id' => $categoryIds['automation-ai'],
                'slug' => 'understanding-ai-responses',
                'type' => 'article',
                'title_fr' => 'Comment fonctionne l\'IA et que peut-elle faire ?',
                'title_en' => 'How does the AI work and what can it do?',
                'excerpt_fr' => 'Comprendre l\'assistant IA : réponses automatiques, Test Chat et rédaction assistée.',
                'excerpt_en' => 'Understand the AI assistant: auto-responses, Test Chat, and assisted writing.',
                'content_fr' => <<<'HTML'
<h2>L\'IA dans WhatsAppBizAI</h2>
<p>L\'assistant IA (basé sur Gemini) est intégré à plusieurs endroits de la plateforme :</p>

<h2>1. Réponses automatiques aux conversations</h2>
<p>Quand un client vous envoie un message WhatsApp, l\'IA peut répondre automatiquement en se basant sur :</p>
<ul>
<li>Le <strong>prompt système</strong> que vous configurez dans les paramètres (personnalité, ton, règles).</li>
<li>Les <strong>documents IA</strong> (texte libre) que vous fournissez comme contexte.</li>
<li>L\'historique de la conversation.</li>
</ul>

<h2>2. Test Chat IA</h2>
<p>Le module <strong>Test Chat IA</strong> (barre latérale) vous permet de simuler une conversation WhatsApp sans connecter un vrai compte. C\'est idéal pour :</p>
<ul>
<li>Tester les réponses de l\'IA avant de mettre le bot en production.</li>
<li>Ajuster le prompt système.</li>
<li>Vérifier que l\'IA comprend votre activité.</li>
</ul>

<h2>3. Rédaction assistée</h2>
<p>Dans le module <strong>Rétention</strong>, l\'IA peut rédiger des messages de relance personnalisés en se basant sur l\'objectif (fidélisation, upsell, win-back, parrainage) et le profil du contact.</li>

<h2>Ce que l\'IA ne fait pas</h2>
<ul>
<li>Elle ne valide pas les paiements.</li>
<li>Elle ne modifie pas vos factures ou devis.</li>
<li>Elle ne remplace pas votre jugement commercial.</li>
</ul>
HTML,
                'content_en' => <<<'HTML'
<h2>AI in WhatsAppBizAI</h2>
<p>The AI assistant (powered by Gemini) is integrated into several parts of the platform:</p>

<h2>1. Automatic conversation responses</h2>
<p>When a client sends you a WhatsApp message, the AI can respond automatically based on:</p>
<ul>
<li>The <strong>system prompt</strong> you configure in settings (personality, tone, rules).</li>
<li>The <strong>AI documents</strong> (free text) you provide as context.</li>
<li>The conversation history.</li>
</ul>

<h2>2. Test Chat AI</h2>
<p>The <strong>Test Chat AI</strong> module (sidebar) lets you simulate a WhatsApp conversation without connecting a real account. It is ideal for:</p>
<ul>
<li>Testing AI responses before going live.</li>
<li>Adjusting the system prompt.</li>
<li>Verifying the AI understands your business.</li>
</ul>

<h2>3. Assisted writing</h2>
<p>In the <strong>Retention</strong> module, the AI can write personalized follow-up messages based on the objective (loyalty, upsell, win-back, referral) and the contact\'s profile.</p>

<h2>What AI does not do</h2>
<ul>
<li>It does not validate payments.</li>
<li>It does not modify your invoices or quotes.</li>
<li>It does not replace your business judgment.</li>
</ul>
HTML,
                'meta_title_fr' => 'Comment fonctionne l\'IA dans WhatsAppBizAI',
                'meta_title_en' => 'How AI works in WhatsAppBizAI',
                'meta_description_fr' => 'Comprenez l\'assistant IA : réponses automatiques, Test Chat et rédaction assistée dans WhatsAppBizAI.',
                'meta_description_en' => 'Understand the AI assistant: auto-responses, Test Chat, and assisted writing in WhatsAppBizAI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(6),
                'sort_order' => 1,
                'views' => 143,
                'difficulty' => 'beginner',
                'reading_minutes' => 4,
                'steps' => null,
            ],
            [
                'help_category_id' => $categoryIds['automation-ai'],
                'slug' => 'configure-ai-prompt',
                'type' => 'tutorial',
                'title_fr' => 'Configurer le prompt de votre assistant IA',
                'title_en' => 'Configure your AI assistant\'s prompt',
                'excerpt_fr' => 'Comment personnaliser le comportement de l\'IA pour qu\'elle réponde comme un vrai membre de votre équipe.',
                'excerpt_en' => 'How to customize the AI\'s behavior so it responds like a real member of your team.',
                'content_fr' => <<<'HTML'
<h2>Où se trouve le prompt ?</h2>
<p>Le prompt système est configuré dans <strong>Paramètres → Mon entreprise</strong>, dans la section <strong>Intelligence Artificielle</strong>. C\'est un champ de texte riche (RichEditor) où vous décrivez :</p>
<ul>
<li>Qui est l\'IA (ex: "Vous êtes l\'assistant commercial de Coiffure Élégance").</li>
<li>Quel ton adopter (professionnel, amical, formel).</li>
<li>Quelles règles suivre (ne pas divulguer les prix à des concurrents, toujours proposer un rendez-vous, etc.).</li>
<li>Des informations sur vos services et tarifs.</li>
</ul>

<h2>Conseils pour un bon prompt</h2>
<ul>
<li>Soyez spécifique : plus le contexte est précis, meilleures seront les réponses.</li>
<li>Définissez des limites : dites à l\'IA ce qu\'elle ne doit pas faire.</li>
<li>Incluez vos tarifs et conditions pour que l\'IA puisse renseigner les clients.</li>
<li>Testez avec le <strong>Test Chat IA</strong> avant d\'activer en production.</li>
<li>Mettez à jour le prompt quand vos services ou tarifs changent.</li>
</ul>

<h2>Exemple de prompt</h2>
<p>"Vous êtes l\'assistant commercial de [Nom]. Vous êtes amical et professionnel. Vous pouvez proposer nos services suivants : [liste]. Les prix commencent à [prix]. Pour les devis, invitez le client à fournir plus de détails. Ne divulguez jamais les prix aux concurrents."</p>

<h2>Testez votre prompt</h2>
<p>Après modification, ouvrez le <strong>Test Chat IA</strong> depuis la barre latéral et posez des questions typiques de vos clients. Vérifiez que les réponses sont pertinentes et dans le bon ton.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>Where is the prompt?</h2>
<p>The system prompt is configured in <strong>Settings → My Business</strong>, in the <strong>Artificial Intelligence</strong> section. It is a rich text field where you describe:</p>
<ul>
<li>Who the AI is (e.g., "You are the sales assistant for Coiffure Élégance").</li>
<li>What tone to use (professional, friendly, formal).</li>
<li>What rules to follow (don\'t share prices with competitors, always propose an appointment, etc.).</li>
<li>Information about your services and pricing.</li>
</ul>

<h2>Tips for a good prompt</h2>
<ul>
<li>Be specific: the more precise the context, the better the responses.</li>
<li>Set boundaries: tell the AI what it must not do.</li>
<li>Include your pricing and conditions so the AI can inform clients.</li>
<li>Test with <strong>Test Chat AI</strong> before going live.</li>
<li>Update the prompt when your services or pricing change.</li>
</ul>

<h2>Example prompt</h2>
<p>"You are the sales assistant for [Name]. You are friendly and professional. You can offer the following services: [list]. Prices start at [price]. For quotes, ask the client to provide more details. Never share prices with competitors."</p>

<h2>Test your prompt</h2>
<p>After editing, open <strong>Test Chat AI</strong> from the sidebar and ask typical customer questions. Verify that the responses are relevant and in the right tone.</p>
HTML,
                'meta_title_fr' => 'Configurer le prompt IA sur WhatsAppBizAI',
                'meta_title_en' => 'Configure the AI prompt in WhatsAppBizAI',
                'meta_description_fr' => 'Tutoriel pour personnaliser le prompt de l\'assistant IA et adapter ses réponses à votre activité.',
                'meta_description_en' => 'Tutorial for customizing the AI assistant\'s prompt and adapting its responses to your business.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'sort_order' => 2,
                'views' => 121,
                'difficulty' => 'intermediate',
                'reading_minutes' => 5,
                'steps' => [
                    ['title_fr' => 'Ouvrir les paramètres entreprise', 'title_en' => 'Open business settings', 'description_fr' => 'Allez dans Mon entreprise depuis le menu.', 'description_en' => 'Go to My Business from the menu.', 'icon' => '⚙️'],
                    ['title_fr' => 'Trouver la section IA', 'title_en' => 'Find the AI section', 'description_fr' => 'Le champ "Prompt IA Gemini" est en bas du formulaire.', 'description_en' => 'The "Gemini AI Prompt" field is at the bottom of the form.', 'icon' => '🤖'],
                    ['title_fr' => 'Rédiger le prompt', 'title_en' => 'Write the prompt', 'description_fr' => 'Décrivez le rôle, le ton et les règles de l\'IA.', 'description_en' => 'Describe the AI\'s role, tone, and rules.', 'icon' => '✍️'],
                    ['title_fr' => 'Tester avec Test Chat', 'title_en' => 'Test with Test Chat', 'description_fr' => 'Ouvrez le Test Chat IA et posez des questions tests.', 'description_en' => 'Open Test Chat AI and ask test questions.', 'icon' => '🧪'],
                ],
            ],
            [
                'help_category_id' => $categoryIds['automation-ai'],
                'slug' => 'retention-campaigns',
                'type' => 'guide',
                'title_fr' => 'Guide : lancer une campagne de rétention',
                'title_en' => 'Guide: launch a retention campaign',
                'excerpt_fr' => 'Comment créer des campagnes ciblées (fidélisation, win-back, upsell, parrainage) avec l\'aide de l\'IA.',
                'excerpt_en' => 'How to create targeted campaigns (loyalty, win-back, upsell, referral) with AI assistance.',
                'content_fr' => <<<'HTML'
<h2>Le module Rétention</h2>
<p>Le module <strong>Rétention</strong> vous permet d\'envoyer des messages ciblés à différents segments de contacts via WhatsApp. L\'IA peut rédiger le message pour vous.</p>

<h2>Les 4 types de campagne</h2>
<ul>
<li><strong>🔒 Rétention</strong> — Fidéliser les clients actifs avec une offre spéciale.</li>
<li><strong>📈 Upsell</strong> — Proposer des services premium aux clients existants.</li>
<li><strong>🔄 Win-back</strong> — Réactiver les clients inactifs depuis 30+ jours.</li>
<li><strong>👥 Parrainage</strong> — Demander aux clients de recommander votre service.</li>
</ul>

<h2>Les segments cibles</h2>
<ul>
<li><strong>Clients inactifs</strong> — Pas de contact depuis 30+ jours.</li>
<li><strong>Tous les clients</strong> — L\'ensemble de vos clients actifs.</li>
<li><strong>Prospects</strong> — Les contacts avec le statut "prospect".</li>
<li><strong>Clients à forte valeur</strong> — Plus de 100 000 XAF facturés.</li>
</ul>

<h2>Étapes pour lancer une campagne</h2>
<ol>
<li>Allez dans <strong>Rétention</strong> depuis le menu latéral.</li>
<li>Choisissez le type de campagne.</li>
<li>Sélectionnez le segment cible.</li>
<li>Entrez l\'objectif (ex: "-20% sur les services premium").</li>
<li>Cliquez sur "🤖 Rédiger avec l\'IA" pour générer un message.</li>
<li>Vérifiez et personnalisez le message.</li>
<li>Cliquez sur "📤 Envoyer la campagne".</li>
</ol>

<h2>Prérequis</h2>
<p>Votre WhatsApp doit être connecté (pas en mode sandbox) pour que les messages soient réellement envoyés.</p>
HTML,
                'content_en' => <<<'HTML'
<h2>The Retention module</h2>
<p>The <strong>Retention</strong> module lets you send targeted messages to different contact segments via WhatsApp. The AI can draft the message for you.</p>

<h2>The 4 campaign types</h2>
<ul>
<li><strong>🔒 Retention</strong> — Loyalty offers for active clients.</li>
<li><strong>📈 Upsell</strong> — Propose premium services to existing clients.</li>
<li><strong>🔄 Win-back</strong> — Re-engage clients inactive for 30+ days.</li>
<li><strong>👥 Referral</strong> — Ask clients to recommend your service.</li>
</ul>

<h2>Target segments</h2>
<ul>
<li><strong>Inactive clients</strong> — No contact for 30+ days.</li>
<li><strong>All clients</strong> — All your active clients.</li>
<li><strong>Prospects</strong> — Contacts with "prospect" status.</li>
<li><strong>High-value clients</strong> — Over 100,000 XAF invoiced.</li>
</ul>

<h2>Steps to launch a campaign</h2>
<ol>
<li>Go to <strong>Retention</strong> from the sidebar.</li>
<li>Choose the campaign type.</li>
<li>Select the target segment.</li>
<li>Enter the objective (e.g., "-20% on premium services").</li>
<li>Click "🤖 Draft with AI" to generate a message.</li>
<li>Review and customize the message.</li>
<li>Click "📤 Send campaign".</li>
</ol>

<h2>Prerequisites</h2>
<p>Your WhatsApp must be connected (not in sandbox mode) for messages to be actually sent.</p>
HTML,
                'meta_title_fr' => 'Lancer une campagne de rétention sur WhatsAppBizAI',
                'meta_title_en' => 'Launch a retention campaign in WhatsAppBizAI',
                'meta_description_fr' => 'Guide pour créer et envoyer des campagnes de rétention, win-back et parrainage avec l\'IA.',
                'meta_description_en' => 'Guide for creating and sending retention, win-back, and referral campaigns with AI.',
                'author_name' => 'WhatsAppBizAI',
                'is_published' => true,
                'published_at' => now()->subDays(4),
                'sort_order' => 3,
                'views' => 97,
                'difficulty' => 'intermediate',
                'reading_minutes' => 5,
                'steps' => [
                    ['title_fr' => 'Ouvrir le module Rétention', 'title_en' => 'Open the Retention module', 'description_fr' => 'Cliquez sur "Rétention" dans le menu.', 'description_en' => 'Click "Retention" in the menu.', 'icon' => '📢'],
                    ['title_fr' => 'Choisir le type de campagne', 'title_en' => 'Choose campaign type', 'description_fr' => 'Rétention, upsell, win-back ou parrainage.', 'description_en' => 'Retention, upsell, win-back, or referral.', 'icon' => '🎯'],
                    ['title_fr' => 'Sélectionner le segment', 'title_en' => 'Select the segment', 'description_fr' => 'Clients inactifs, tous les clients, prospects ou forte valeur.', 'description_en' => 'Inactive clients, all clients, prospects, or high-value.', 'icon' => '👥'],
                    ['title_fr' => 'Générer le message avec l\'IA', 'title_en' => 'Generate the message with AI', 'description_fr' => 'Cliquez sur "🤖 Rédiger avec l\'IA" puis vérifiez le résultat.', 'description_en' => 'Click "🤖 Draft with AI" then review the result.', 'icon' => '🤖'],
                    ['title_fr' => 'Envoyer la campagne', 'title_en' => 'Send the campaign', 'description_fr' => 'Vérifiez et cliquez sur Envoyer.', 'description_en' => 'Review and click Send.', 'icon' => '📤'],
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
