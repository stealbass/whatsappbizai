# Modifications apportées au projet WhatsAppBizAI

## Fonctionnalités & Évolutions

### 1. SEO — SiteSetting + Sitemap + Robots + JSON-LD

- Création du modèle `SiteSetting` (singleton, cache 1h)
- Filament Resource : 7 onglets (Général, SEO, Réseaux sociaux, Contact, Apparence, Légal, Fonctionnalités)
- 30+ champs : titre, description, mots-clés, logo, favicon, scripts GA, RGPD, réseaux sociaux, etc.
- View Composer `SiteSettingComposer` partage `$site` avec toutes les vues
- Métas dynamiques : `<title>`, meta description/keywords, OG, Twitter Card, canonical, hreflang, robots
- JSON-LD : schema SoftwareApplication + FAQPage (bilingue FR/EN)
- `sitemap.xml` (7 routes dynamiques) + `robots.txt`
- Cache rafraîchi automatiquement à la sauvegarde

### 2. i18n — Internationalisation complète (FR/EN)

- **Admin Filament** : ~300 clés de traduction dans `resources/lang/fr/app.php` et `en/app.php`
- **Client panel** : 24+ vues Blade traduites via `__('app.client.*')` — 150+ clés
- **Public pages** : Landing, pricing, privacy, terms, contact — bilingues avec SEO partial
- **Contrôleurs** : 27 messages flash traduits dans 7 contrôleurs client
- **Locale resolution** : `SetLocale` middleware (session + cookie), `AdminPanelProvider::panel()` pour le panel admin
- **FR/EN switcher** : Widget admin + sélecteur client dans le layout sidebar
- **Correction complète** : Passage de `__()` dans les propriétés statiques → méthodes override (PHP 8.2 compat)
- Audit complet : ~180+ chaînes françaises remplacées, ~120+ nouvelles clés

### 3. AI — Génération de contenu par IA

- **GeminiService** : service Laravel pour l'API Google Gemini
- **Upload de documents** : Migration `ai_documents`, upload PDF/Word/Excel/PPT/text
- **Brouillon AI** : Bouton « Draft with AI » dans les pages Broadcast et Retention (client + admin)
- **Drag-and-drop** dans les paramètres WhatsApp

### 4. Filament — Super Admin Panel

- **Ressources** : Business, Contact, Conversation, Invoice, Quote, Service, Payment, Subscription, User, SiteSetting
- **Widgets** : StatsOverview, RevenueChart, RecentConversations, LanguageSwitcher
- **Pages** : Dashboard, RetentionCampaigns
- **Actions** : PaymentResource → vérification (set `plan_expires_at` sur le business)
- **Navigation** : Groupes Administration / Messagerie / Marketing / Configuration
- **Labels** : Tous les labels via méthodes override avec `__()`

### 5. Client Panel — Vue d'ensemble

- **Dashboard** : stats cards, factures/échéances récentes, bannière de configuration
- **Layout sidebar** : navigation par sections, topbar, responsive, FR/EN
- **CRUD complet** (16+ vues) : Contacts, Invoices, Quotes, Services, Conversations, Settings, Broadcast, Retention
- **PDF** : Génération de factures et devis
- **WhatsApp** : Envoi de messages, relance, conversion devis→facture
- **TVA et remises** sur factures et devis
- **7 contrôleurs** : ContactController, InvoiceController, QuoteController, ServiceController, ConversationController, SettingsController, BroadcastController, RetentionController
- **ClientComposer** : partage `$sidebarStats` et `$business` avec toutes les vues client
- **Inscription** : création `role => 'user'`, redirection vers `/dashboard`
- **Google OAuth** : LoginController avec `redirectToGoogle()`/`handleGoogleCallback()`

### 6. Retention — Séparation Client / Admin

- **Client** : Relance des contacts WhatsApp
- **Admin** : Campagne email pour utilisateurs SaaS (expirés, essai, inactifs)
- Routes : `/client/retention`, `/send`, `/draft-ai`
- Sidebar client : section Marketing avec lien Retention
- **Admin RetentionCampaigns** : Sélection cible + objectif + message + bouton AI + envoi

### 7. WYSIWYG — Intégration éditeurs

- **TinyMCE** : Composant partagé `components/tinymce.blade.php` + fonction `initTinyMCE()`
- CDN initial `cdn.tiny.cloud/1/no-api-key` retourne 403
- Ajout CSS `.tox-notification { display: none !important; }` pour cacher l'avertissement
- Ajout `promotion: false` dans la config
- Ajout handler `focusin` pour éviter le grisé
- Auto-hébergement : extraction de TinyMCE 6.8.6 depuis npm dans `public/vendor/tinymce/`
- Plugins : advlist, code, fullscreen, image, link, lists, preview, table
- **Summernote** : Remplacement de TinyMCE sur la page admin Retention
  - CDN cdnjs (pas d'API key)
  - + jQuery 3.7.1
  - Sync du contenu au submit uniquement (pas de re-render à la frappe)
- **HtmlEditorWidget** : TinyMCE pour les champs légaux SiteSetting (privacy, terms, cookies, footer)

### 8. Pages publiques

- Landing page : Hero, trust bar, mockup demo, features grid, how-it-works, testimonials, stats, pricing (Free/Starter/Business), CTA, contact form, cookie consent, FAQ
- Pages légales : Privacy Policy, Terms & Conditions (bilingues)
- Formulaire de contact : table `contact_messages`, validation, feedback
- Cookie consent banner avec localStorage
- Footer professionnel 4 colonnes

### 9. DB & Modèles

- Enum `users.role` : `admin`, `agent`, `user`
- Enum `contacts.status` : `prospect`, `client`, `inactif`
- Table `ai_documents` pour uploads IA
- Colonne `phone` ajoutée à contacts
- Currencies supportées : XAF, XOF, EUR, USD, GBP, ZAR, MAD, NGN, GHS, KES

## Correctifs techniques

- `Artisan::call()` plutôt que `exec()` pour l'installeur (évite les erreurs Apache)
- Toutes les URLs utilisent le helper `url()` pour compatibilité sous-répertoire
- `ClientComposer` utilise `$sidebarStats` pour ne pas écraser `$stats` du contrôleur
- Les propriétés statiques Filament ne peuvent pas utiliser `__()` → méthodes override
- `maxLength` porté de 1024 à 65535 pour le message HTML de rétention
- Correction des retraits et espaces superflus dans AdminPanelProvider

---

## Session du 8 juillet 2026

### 10. Variables broadcast/retention — Correction d'affichage

- `client/broadcast/index.blade.php` — `{!! '{{nom}}' !!}` → `@php echo '{{nom}}' @endphp` pour les 3 variables
- `client/retention/index.blade.php` — Idem
- `filament/pages/broadcast.blade.php` — Idem (admin)
- `filament/pages/retention.blade.php` — Ajout de la section "Variables disponibles" (manquait dans l'admin)

### 11. Blog posts bilingues (FR/EN)

- `database/seeders/PostSeeder.php` — Ajout des colonnes `_fr`/`_en` pour les 4 articles (title, excerpt, content, meta_title, meta_description)
- Changé `Post::create()` → `Post::updateOrCreate()` pour les mises à jour

### 12. Architecture WhatsApp — Credentials déplacés vers le client

Avant : l'admin devait entrer les credentials WhatsApp (Phone Number ID, Access Token, Business Account ID) pour chaque business — impossible en pratique.
Après : le client configure ses propres credentials dans `client/settings/whatsapp`.

| Fichier | Changement |
|---|---|
| `BusinessResource.php` | Supprimé les 3 champs du formulaire admin. Ajouté `whatsapp_phone_number_id` en colonne cachée du tableau (débogage) |
| `config/whatsapp.php` | Supprimé les 3 variables inutilisées. Gardé `verify_token` et `api_version` |
| `.env` | Supprimé `WHATSAPP_PHONE_NUMBER_ID`, `WHATSAPP_ACCESS_TOKEN`, `WHATSAPP_BUSINESS_ACCOUNT_ID` |
| `.env.example` | Idem + commentaire |
| `InstallController.php` | Supprimé l'écriture des 3 variables |
| `install/index.blade.php` | Supprimé les 3 champs du formulaire d'installation |

### 13. Système d'emails complet (6 Mailables + templates)

Avant : aucun email n'était envoyé.

**Mailables créés :**

| Mailable | Déclencheur | Destinataire |
|---|---|---|
| `WelcomeMail` | Inscription | Nouveau client |
| `SubscriptionActivatedMail` | Paiement validé (callback/webhook Flutterwave) | Client |
| `PaymentPendingMail` | Paiement manuel soumis | Admin |
| `ContactFormMail` | Formulaire contact soumis | Admin |
| `BroadcastMail` | Campagne broadcast admin | Contacts |
| `RetentionMail` | Campagne retention admin | Utilisateurs |

**Templates créés :**

```
resources/views/emails/
├── layouts/email.blade.php           ← layout commun (header WhatsAppBizAI + footer)
├── welcome.blade.php                 ← étapes à suivre + bouton dashboard
├── subscription_activated.blade.php  ← récapitulatif plan/montant/durée
├── payment_pending.blade.php         ← détails du paiement + lien admin
└── contact_form.blade.php            ← nom/email/sujet/message + bouton répondre
```

**Controllers modifiés :**

| Controller | Email envoyé |
|---|---|
| `RegisterController::store` | `WelcomeMail` |
| `PaymentController::callback` | `SubscriptionActivatedMail` |
| `PaymentController::webhook` | `SubscriptionActivatedMail` |
| `PaymentController::manualStore` | `PaymentPendingMail` → admin |
| `PageController::contactStore` | `ContactFormMail` → admin |
| `BroadcastPage::send` | `BroadcastMail` (remplacé `Mail::html()` brut) |
| `RetentionCampaigns::sendCampaign` | `RetentionMail` (remplacé `Mail::html()` brut) |

### 14. SEO — Schema.org Structured Data

**`seo.blade.php` (toutes les pages) :**

| Schema | Contenu |
|---|---|
| **Organization** | Nom, logo, adresse, téléphone, email, date fondation, réseaux sociaux (`sameAs`), `aggregateRating` (4.8/5), 3 reviews (Happi Olivier, Fatima Diallo, Aminata Touré) |
| **SoftwareApplication** | Catégorie BusinessApplication, 4 offres (Free/Starter/Business/Pro) avec prix XAF, `aggregateRating` |
| **FAQPage** | 6 questions dynamiques depuis les lang files FR/EN (homepage uniquement) |

**`blog/show.blade.php` (chaque article) :**

| Schema | Contenu |
|---|---|
| **Article** | headline, description, datePublished, dateModified, author, publisher (logo), image, articleSection, mainEntityOfPage |

### Fichiers créés (16)

```
app/Mail/WelcomeMail.php
app/Mail/SubscriptionActivatedMail.php
app/Mail/PaymentPendingMail.php
app/Mail/ContactFormMail.php
app/Mail/BroadcastMail.php
app/Mail/RetentionMail.php
resources/views/emails/layouts/email.blade.php
resources/views/emails/welcome.blade.php
resources/views/emails/subscription_activated.blade.php
resources/views/emails/payment_pending.blade.php
resources/views/emails/contact_form.blade.php
```

### Fichiers modifiés (14)

```
app/Filament/Resources/BusinessResource.php
app/Filament/Pages/BroadcastPage.php
app/Filament/Pages/RetentionCampaigns.php
app/Http/Controllers/RegisterController.php
app/Http/Controllers/PaymentController.php
app/Http/Controllers/PageController.php
app/Http/Controllers/InstallController.php
config/whatsapp.php
.env
.env.example
resources/views/install/index.blade.php
resources/views/client/broadcast/index.blade.php
resources/views/client/retention/index.blade.php
resources/views/filament/pages/broadcast.blade.php
resources/views/filament/pages/retention.blade.php
resources/views/components/seo.blade.php
resources/views/blog/show.blade.php
database/seeders/PostSeeder.php
```
