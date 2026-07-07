# WhatsAppBizAI 🤖

Back-office IA complet sur WhatsApp pour petites entreprises de services francophones.

> Soumis au **Build with Gemini XPRIZE** — Catégorie: Small Business Services

---

## Stack

- **Laravel 11** (PHP 8.2+)
- **Filament 3** — dashboard back-office
- **Gemini 2.5 Flash** — agent IA conversationnel
- **WhatsApp Cloud API** (Meta) — messagerie
- **MySQL** — base de données
- **DomPDF** — génération PDF devis/factures

---

## Fonctionnalités

- ✅ **Agent IA WhatsApp** — répond automatiquement aux clients (FR/EN)
- ✅ **Devis PDF** — générés et envoyés via WhatsApp
- ✅ **Factures PDF** — avec suivi de paiement
- ✅ **Relances automatiques** — factures échues via cron
- ✅ **CRM intégré** — contacts, historique, statuts
- ✅ **Catalogue services** — tarifs configurables par entreprise
- ✅ **Dashboard Filament** — conversations, devis, factures, contacts
- ✅ **Broadcast WhatsApp** — campagnes segmentées (clients/prospects)
- ✅ **Campagnes de rétention** — e-mails HTML avec aperçu pré-envoi
- ✅ **WYSIWYG HTML** — éditeur riche (Quill.js) + toggle source `</>` + aperçu iframe
- ✅ **Multi-tenant** — chaque entreprise isolée (BusinessScope)
- ✅ **Portail client** — accès sans login via token, devis/factures
- ✅ **Install wizard** — `/install` pour déploiement sans SSH
- ✅ **Abonnements + paiements** — Flutterwave (MoMo, Orange, Wave, carte)
- ✅ **Bilingue FR/EN** — détection automatique par pays

---

## Installation (locale ou Alwaysdata)

### 1. Cloner et installer les dépendances
```bash
git clone https://github.com/stealbass/whatsappbizai.git
cd whatsappbizai
composer install
```

### 2. Configurer l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

Remplir dans `.env` :
- `DB_*` — vos identifiants MySQL
- `WHATSAPP_PHONE_NUMBER_ID` — depuis Meta for Developers
- `WHATSAPP_ACCESS_TOKEN` — token permanent Meta
- `WHATSAPP_VERIFY_TOKEN` — chaîne secrète de votre choix
- `GEMINI_API_KEY` — depuis Google AI Studio

### 3. Migrations et données de démo
```bash
php artisan migrate
php artisan db:seed
```

### 4. Lien storage (pour les PDFs)
```bash
php artisan storage:link
```

### 5. Accéder au dashboard
```
http://localhost/admin
Email    : admin@whatsappbizai.com
Password : password
```

---

## Configuration WhatsApp (Meta)

1. Créez une app sur [developers.facebook.com](https://developers.facebook.com)
2. Ajoutez le produit **WhatsApp Business**
3. Configurez le webhook :
   - URL : `https://votre-domaine.com/api/webhook/whatsapp`
   - Token : la valeur de `WHATSAPP_VERIFY_TOKEN` dans votre `.env`
   - Champs à souscrire : `messages`

---

## Déploiement Alwaysdata

1. Pointez le document root du site vers `/public`
2. PHP version : **8.2** ou supérieure (configurable dans l'admin Alwaysdata)
3. Créez la base MySQL depuis l'admin Alwaysdata
4. Uploadez les fichiers via FTP ou SSH
5. Lancez `composer install --no-dev` en SSH
6. Configurez le cron Alwaysdata :
   ```
   * * * * * cd /home/LOGIN/www && php artisan schedule:run >> /dev/null 2>&1
   ```

---

## Structure du projet

```
app/
├── Console/Commands/    # SendOverdueReminders
├── Filament/Resources/  # Dashboard: Conversations, Contacts, Devis, Factures, Services
├── Http/Controllers/    # WhatsAppWebhookController
├── Jobs/               # ProcessWhatsAppMessage (queue)
├── Models/             # Business, Contact, Conversation, Message, Invoice, Quote, Service
├── Providers/          # AdminPanelProvider (Filament)
└── Services/           # GeminiService, WhatsAppService, DocumentService, ReminderService
database/
├── migrations/         # Toutes les tables
└── seeders/            # Données de démo
resources/views/pdf/    # Templates Blade pour PDF factures/devis
routes/
├── api.php             # Webhook WhatsApp
└── web.php             # Redirect vers /admin
```

---

## Licence

MIT — © 2026 Happi Olivier
