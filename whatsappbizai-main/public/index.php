<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check if .env exists — if not, create minimal one so Laravel can boot
|--------------------------------------------------------------------------
*/
$envFile    = __DIR__.'/../.env';
$envExample = __DIR__.'/../.env.example';

if (!file_exists($envFile) && file_exists($envExample)) {
    $key = 'base64:' . base64_encode(random_bytes(32));
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $url = "http://{$host}/testwebsite/whatsappbizai-main/public";
    file_put_contents($envFile, <<<ENV
APP_NAME=WhatsAppBizAI
APP_ENV=local
APP_KEY={$key}
APP_DEBUG=true
APP_URL={$url}
APP_TIMEZONE=Africa/Douala
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=whatsappbizai
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=file
APP_INSTALLED=false
ENV
    );
}

/*
|--------------------------------------------------------------------------
| Check if app is installed
|--------------------------------------------------------------------------
*/
$installed = false;
if (file_exists($envFile)) {
    $content = file_get_contents($envFile);
    $installed = (bool) preg_match('/^APP_INSTALLED=true$/m', $content);
}

if (!$installed) {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $uri = rtrim($uri, '/');

    if (!str_ends_with($uri, '/install') && !str_contains($uri, '/install/')) {
        header('Location: /testwebsite/whatsappbizai-main/public/install');
        exit;
    }

    require __DIR__.'/../vendor/autoload.php';

    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    (require_once __DIR__.'/../bootstrap/app.php')
        ->handleRequest(Illuminate\Http\Request::capture());
    exit;
}

/*
|--------------------------------------------------------------------------
| Normal Laravel bootstrap
|--------------------------------------------------------------------------
*/
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Illuminate\Http\Request::capture());
