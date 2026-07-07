#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────
# WhatsAppBizAI — Script d'installation rapide
# Usage : bash install.sh
# ─────────────────────────────────────────────────────────────
set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}  WhatsAppBizAI — Installation${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# 1. Vérification PHP
php_version=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo -e "${GREEN}✓ PHP $php_version détecté${NC}"

# 2. Dépendances Composer
echo -e "\n${YELLOW}→ Installation des dépendances Composer...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Fichier .env
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${GREEN}✓ .env créé depuis .env.example${NC}"
else
    echo -e "${GREEN}✓ .env existant conservé${NC}"
fi

# 4. Clé d'application
php artisan key:generate --ansi
echo -e "${GREEN}✓ Clé d'application générée${NC}"

# 5. Migrations
echo -e "\n${YELLOW}→ Migrations de la base de données...${NC}"
php artisan migrate --ansi

# 6. Seeder optionnel
read -p "Charger les données de démo ? (o/N) " demo
if [[ "$demo" =~ ^[Oo]$ ]]; then
    php artisan db:seed --ansi
    echo -e "${GREEN}✓ Données de démo chargées${NC}"
fi

# 7. Lien storage
php artisan storage:link --ansi
echo -e "${GREEN}✓ Lien storage créé${NC}"

# 8. Cache de configuration
php artisan config:cache --ansi
php artisan route:cache --ansi
php artisan view:cache --ansi
echo -e "${GREEN}✓ Cache optimisé${NC}"

echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}  ✅ Installation terminée !${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo "  Dashboard : http://votre-domaine.com/admin"
echo "  Email     : admin@whatsappbizai.com"
echo "  Password  : password  ← CHANGEZ-LE"
echo ""
echo -e "${YELLOW}  N'oubliez pas de configurer dans .env :${NC}"
echo "  - DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD"
echo "  - WHATSAPP_PHONE_NUMBER_ID, WHATSAPP_ACCESS_TOKEN"
echo "  - WHATSAPP_VERIFY_TOKEN"
echo "  - GEMINI_API_KEY"
echo ""
echo -e "${YELLOW}  Cron Alwaysdata (Tâches planifiées) :${NC}"
echo "  * * * * * cd ~/www && php artisan schedule:run >> /dev/null 2>&1"
echo ""
