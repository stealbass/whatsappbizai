# Guide de déploiement — Alwaysdata

## Prérequis

- Compte Alwaysdata (gratuit ou payant)
- PHP 8.2 activé dans l'admin
- Base de données MySQL créée
- Accès SSH activé

---

## Étape 1 — Préparer l'hébergement

Dans l'admin Alwaysdata :

1. **Sites** → Ajouter un site
   - Type : Apache + PHP
   - Adresses : `whatsappbizai.com www.whatsappbizai.com`
   - Répertoire racine : `/public` (important !)

2. **Bases de données** → MySQL → Créer
   - Notez : host, nom, user, password

3. **SSH** → Activer l'accès SSH

---

## Étape 2 — Déploiement initial via SSH

```bash
# Connectez-vous en SSH
ssh login@ssh-login.alwaysdata.net

# Allez dans le répertoire web
cd ~/www

# Clonez le repo (ou uploadez le zip)
git clone https://github.com/stealbass/whatsappbizai.git .

# Installez les dépendances PHP (sans dev)
composer install --no-dev --optimize-autoloader

# Copiez et configurez le .env
cp .env.example .env
nano .env
# → Remplissez DB_*, WHATSAPP_*, GEMINI_API_KEY, APP_URL

# Générez la clé d'application
php artisan key:generate

# Exécutez les migrations et le seeder
php artisan migrate --seed

# Créez le lien symbolique pour le storage (PDFs publics)
php artisan storage:link

# Optimisez pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize
```

---

## Étape 3 — Configurer le Cron Job

Dans l'admin Alwaysdata → **Tâches planifiées** → Ajouter :

```
* * * * * cd ~/www && php artisan schedule:run >> /dev/null 2>&1
```

Cela active :
- Relances factures échues (09h00 chaque jour)
- Traitement de la queue de messages WhatsApp (chaque minute)

---

## Étape 4 — Configurer le webhook WhatsApp

1. Allez sur [developers.facebook.com](https://developers.facebook.com)
2. Votre app → WhatsApp → Configuration
3. Webhook URL : `https://whatsappbizai.com/api/webhook/whatsapp`
4. Verify Token : valeur de `WHATSAPP_VERIFY_TOKEN` dans votre `.env`
5. Champs à souscrire : `messages` ✓

---

## Étape 5 — GitHub Actions (déploiements automatiques)

Ajoutez ces secrets dans votre repo GitHub (Settings → Secrets → Actions) :

| Secret | Valeur |
|---|---|
| `ALWAYSDATA_HOST` | `ssh-votre-login.alwaysdata.net` |
| `ALWAYSDATA_USER` | Votre login Alwaysdata |
| `ALWAYSDATA_PASSWORD` | Votre mot de passe SSH |
| `ALWAYSDATA_PATH` | `~/www` |

À chaque push sur `main`, le déploiement se fait automatiquement.

---

## Accès au dashboard

```
https://whatsappbizai.com/admin
Email    : admin@whatsappbizai.com
Mot de passe : password  ← CHANGEZ-LE IMMÉDIATEMENT
```

---

## Variables .env complètes pour production

```env
APP_NAME=WhatsAppBizAI
APP_ENV=production
APP_KEY=           # généré par php artisan key:generate
APP_DEBUG=false
APP_URL=https://whatsappbizai.com
APP_TIMEZONE=Africa/Douala

DB_CONNECTION=mysql
DB_HOST=mysql-votre-login.alwaysdata.net
DB_PORT=3306
DB_DATABASE=votre_login_whatsappbizai
DB_USERNAME=votre_login
DB_PASSWORD=votre_mot_de_passe

SESSION_DRIVER=file
QUEUE_CONNECTION=database
CACHE_STORE=file

WHATSAPP_PHONE_NUMBER_ID=
WHATSAPP_ACCESS_TOKEN=
WHATSAPP_VERIFY_TOKEN=chaine_secrete_de_votre_choix
WHATSAPP_BUSINESS_ACCOUNT_ID=
WHATSAPP_API_VERSION=v20.0

GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.5-flash

MAIL_MAILER=smtp
MAIL_HOST=smtp.alwaysdata.net
MAIL_PORT=587
MAIL_USERNAME=votre_email@votredomaine.com
MAIL_PASSWORD=votre_mot_de_passe_smtp
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@whatsappbizai.com
MAIL_FROM_NAME=WhatsAppBizAI

FLUTTERWAVE_PUBLIC_KEY=FLWPUBK_TEST-xxxxxxxxxxxx-X
FLUTTERWAVE_SECRET_KEY=FLWSECK_TEST-xxxxxxxxxxxx-X
FLUTTERWAVE_WEBHOOK_SECRET=votre_secret_webhook
```
