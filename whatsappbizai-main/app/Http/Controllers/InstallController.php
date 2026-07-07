<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstallController extends Controller
{
    protected string $basePath;
    protected string $envPath;

    public function __construct()
    {
        $this->basePath = base_path();
        $this->envPath = base_path('.env');
    }

    public function show()
    {
        if ($this->isInstalled()) {
            return redirect('/admin');
        }
        return view('install.index');
    }

    public function checkRequirements()
    {
        $checks = [
            'php_version' => [
                'label' => 'PHP >= 8.2',
                'ok'    => version_compare(PHP_VERSION, '8.2.0', '>='),
                'detail' => 'Version actuelle : ' . PHP_VERSION,
            ],
            'ext_json' => [
                'label' => 'Extension JSON',
                'ok'    => extension_loaded('json'),
            ],
            'ext_mbstring' => [
                'label' => 'Extension mbstring',
                'ok'    => extension_loaded('mbstring'),
            ],
            'ext_xml' => [
                'label' => 'Extension XML',
                'ok'    => extension_loaded('xml'),
            ],
            'ext_curl' => [
                'label' => 'Extension cURL',
                'ok'    => extension_loaded('curl'),
            ],
            'ext_pdo_mysql' => [
                'label' => 'Extension PDO MySQL',
                'ok'    => extension_loaded('pdo_mysql'),
            ],
            'ext_opcache' => [
                'label' => 'OPcache',
                'ok'    => true,
                'detail' => extension_loaded('opcache') ? 'Activé' : 'Recommandé',
            ],
            'writable_storage' => [
                'label' => 'Dossier storage/ accessible en écriture',
                'ok'    => is_writable($this->basePath . '/storage'),
            ],
            'writable_bootstrap_cache' => [
                'label' => 'Dossier bootstrap/cache/ accessible en écriture',
                'ok'    => is_writable($this->basePath . '/bootstrap/cache'),
            ],
            'composer' => [
                'label' => 'Composer disponible',
                'ok'    => true,
                'detail' => 'Vérifié lors de l\'étape préalable',
            ],
        ];

        $allOk = collect($checks)->every(fn($c) => $c['ok']);

        return response()->json(['checks' => $checks, 'all_ok' => $allOk]);
    }

    public function testConnection(Request $request)
    {
        $request->validate([
            'db_host'     => 'required|string',
            'db_port'     => 'required|integer',
            'db_name'     => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            $pdo = new \PDO(
                "mysql:host={$request->db_host};port={$request->db_port};charset=utf8mb4",
                $request->db_username,
                $request->db_password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$request->db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$request->db_name}`");

            return response()->json(['success' => true, 'message' => 'Connexion réussie. Base de données prête.']);
        } catch (\PDOException $e) {
            return response()->json(['success' => false, 'message' => 'Erreur de connexion : ' . $e->getMessage()]);
        }
    }

    public function setup(Request $request)
    {
        $request->validate([
            'app_name'    => 'required|string',
            'app_url'     => 'required|url',
            'db_host'     => 'required|string',
            'db_port'     => 'required|integer',
            'db_name'     => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        $steps = [];
        $step  = 0;

        try {
            // Step 1: Create .env
            $step++;
            $envContent = $this->buildEnv($request);
            file_put_contents($this->envPath, $envContent);

            // Reload DB config in memory (the .env was just written, but PHP already loaded old values)
            config(['database.connections.mysql.database' => $request->db_name]);
            config(['database.connections.mysql.username' => $request->db_username]);
            config(['database.connections.mysql.password' => $request->db_password]);
            config(['database.connections.mysql.host' => $request->db_host]);
            config(['database.connections.mysql.port' => $request->db_port]);
            DB::purge('mysql');

            $steps[] = ['step' => $step, 'label' => 'Fichier .env créé', 'ok' => true];

            // Step 2: Clear caches
            $step++;
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            $steps[] = ['step' => $step, 'label' => 'Cache vidé', 'ok' => true];

            // Step 3: Generate APP_KEY
            $step++;
            $keyOutput = Artisan::call('key:generate', ['--force' => true]);
            $steps[] = ['step' => $step, 'label' => 'Clé d\'application générée', 'ok' => true, 'detail' => Artisan::output()];

            // Step 4: Run migrations
            $step++;
            $migrationOutput = Artisan::call('migrate', ['--force' => true]);
            if ($migrationOutput !== 0) {
                throw new \RuntimeException("Migrations échouées:\n" . Artisan::output());
            }
            $steps[] = ['step' => $step, 'label' => 'Migrations exécutées', 'ok' => true, 'detail' => Artisan::output()];

            // Step 5: Seed demo data
            $step++;
            Artisan::call('db:seed', ['--force' => true]);
            $steps[] = ['step' => $step, 'label' => 'Données de démo chargées', 'ok' => true, 'detail' => Artisan::output()];

            // Step 6: Storage link
            $step++;
            $publicStorage = $this->basePath . '/public/storage';
            if (!file_exists($publicStorage)) {
                Artisan::call('storage:link');
            }
            $steps[] = ['step' => $step, 'label' => 'Lien symbolique storage créé', 'ok' => true];

            // Step 7: Mark as installed
            $step++;
            $this->markInstalled();
            $steps[] = ['step' => $step, 'label' => 'Application marquée comme installée', 'ok' => true];

            return response()->json([
                'success'   => true,
                'steps'     => $steps,
                'login'     => 'admin@whatsappbizai.com',
                'password'  => 'password',
                'admin_url' => rtrim($request->app_url, '/') . '/admin',
            ]);

        } catch (\Exception $e) {
            $steps[] = ['step' => $step, 'label' => 'Erreur', 'ok' => false, 'detail' => $e->getMessage()];
            return response()->json(['success' => false, 'steps' => $steps, 'message' => $e->getMessage()]);
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────

    protected function isInstalled(): bool
    {
        if (!file_exists($this->envPath)) return false;
        $content = file_get_contents($this->envPath);
        return (bool) preg_match('/^APP_INSTALLED=true$/m', $content);
    }

    protected function buildEnv(Request $request): string
    {
        $appKey = 'base64:' . base64_encode(random_bytes(32));

        return <<<ENV
APP_NAME={$request->app_name}
APP_ENV=local
APP_KEY={$appKey}
APP_DEBUG=true
APP_URL={$request->app_url}
APP_TIMEZONE=Africa/Douala
APP_INSTALLED=false

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST={$request->db_host}
DB_PORT={$request->db_port}
DB_DATABASE={$request->db_name}
DB_USERNAME={$request->db_username}
DB_PASSWORD={$request->db_password}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=file

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@whatsappbizai.com"
MAIL_FROM_NAME="\${APP_NAME}"

WHATSAPP_PHONE_NUMBER_ID={$request->input('whatsapp_phone_number_id', '')}
WHATSAPP_ACCESS_TOKEN={$request->input('whatsapp_access_token', '')}
WHATSAPP_VERIFY_TOKEN={$request->input('whatsapp_verify_token', 'your_custom_verify_token_here')}
WHATSAPP_BUSINESS_ACCOUNT_ID={$request->input('whatsapp_business_account_id', '')}
WHATSAPP_API_VERSION=v20.0

GEMINI_API_KEY={$request->input('gemini_api_key', '')}
GEMINI_MODEL=gemini-2.5-flash

FLUTTERWAVE_PUBLIC_KEY={$request->input('flutterwave_public_key', '')}
FLUTTERWAVE_SECRET_KEY={$request->input('flutterwave_secret_key', '')}
FLUTTERWAVE_WEBHOOK_SECRET={$request->input('flutterwave_webhook_secret', '')}

ACTIVITY_LOGGER_ENABLED=true
ENV;
    }

    protected function markInstalled(): void
    {
        if (!file_exists($this->envPath)) return;

        $content = file_get_contents($this->envPath);
        $content = preg_replace('/^APP_INSTALLED=false$/m', 'APP_INSTALLED=true', $content);

        if (!str_contains($content, 'APP_INSTALLED=true')) {
            $content = rtrim($content) . "\nAPP_INSTALLED=true\n";
        }

        file_put_contents($this->envPath, $content);
    }
}
