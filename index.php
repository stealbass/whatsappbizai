<?php
/**
 * WhatsAppBizAI — Root entry point
 * Handles 3 scenarios:
 *   A) No vendor/autoload.php → self-contained composer install page
 *   B) vendor exists but not installed → redirect to public/install
 *   C) Installed → redirect to public/
 */

$publicUrl = '/testwebsite/whatsappbizai-main/public';

/*
|--------------------------------------------------------------------------
| A) vendor/autoload.php doesn't exist → serve self-contained installer
|--------------------------------------------------------------------------
*/
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {

    $action = $_POST['install_action'] ?? null;

    /* ── AJAX handler: run composer install ─────────────────────────────── */
    if ($action === 'composer_install') {
        header('Content-Type: application/json; charset=utf-8');
        error_reporting(0);

        // Find PHP binary — prefer PHP 8.2 for Laravel 11 compatibility
        $php = PHP_BINARY;
        $phpPaths = [
            'C:\\php82\\php.exe',
            'C:\\xampp\\php\\php.exe',
            'C:\\php\\php.exe',
            'php',
        ];
        foreach ($phpPaths as $p) {
            if (file_exists($p)) { $php = $p; break; }
        }

        // Find composer.phar — run directly with PHP (avoids PATH issues with .bat)
        $composerPhar = null;
        $pharPaths = [
            'C:\\ProgramData\\ComposerSetup\\bin\\composer.phar',
            __DIR__ . '\\composer.phar',
        ];
        foreach ($pharPaths as $p) {
            if (file_exists($p)) { $composerPhar = $p; break; }
        }

        if (!$php || !file_exists($php)) {
            echo json_encode(['ok' => false, 'msg' => "PHP introuvable.\nCherché : " . implode(', ', $phpPaths)]);
            exit;
        }
        if (!$composerPhar) {
            echo json_encode(['ok' => false, 'msg' => "Composer introuvable.\nCherché : " . implode(', ', $pharPaths) . "\n\nInstallez-le depuis : https://getcomposer.org/download"]);
            exit;
        }

        $cmd = "cd /d \"" . __DIR__ . "\" && \"{$php}\" \"{$composerPhar}\" install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs 2>&1";
        exec($cmd, $output, $exitCode);

        echo json_encode([
            'ok'   => $exitCode === 0,
            'msg'  => implode("\n", $output),
            'code' => $exitCode,
        ]);
        exit;
    }

    /* ── HTML page ──────────────────────────────────────────────────────── */
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsAppBizAI — Installation</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#0f172a;color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .card{background:#1e293b;border-radius:16px;padding:48px;width:100%;max-width:520px;box-shadow:0 25px 60px rgba(0,0,0,.5)}
        .logo{text-align:center;margin-bottom:32px}
        .logo h1{font-size:28px;font-weight:800}
        .logo h1 span{color:#0ea5e9}
        .logo p{color:#94a3b8;font-size:14px;margin-top:8px}
        .info{background:#0f172a;border:1px solid #334155;border-radius:10px;padding:20px;margin-bottom:24px;font-size:13px;color:#94a3b8;line-height:1.8}
        .info strong{color:#fff}
        .btn{display:block;width:100%;padding:16px;background:#0ea5e9;color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:700;cursor:pointer;transition:background .2s}
        .btn:hover{background:#0284c7}
        .btn:disabled{background:#334155;color:#64748b;cursor:not-allowed}
        .progress{display:none;margin-top:20px}
        .bar{width:100%;height:6px;background:#334155;border-radius:3px;overflow:hidden}
        .bar-fill{height:100%;background:linear-gradient(90deg,#0ea5e9,#22c55e);width:0%;transition:width .3s;border-radius:3px}
        .bar-text{font-size:12px;color:#94a3b8;margin-top:8px;text-align:center}
        .log{display:none;margin-top:16px;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:14px;font-family:Consolas,monospace;font-size:11px;color:#94a3b8;max-height:250px;overflow-y:auto;white-space:pre-wrap;word-break:break-all}
        .success{display:none;margin-top:24px;background:#166534;border:1px solid #22c55e;border-radius:10px;padding:24px;text-align:center}
        .success h2{font-size:18px;margin-bottom:8px}
        .success p{font-size:13px;color:#bbf7d0}
        .success a{color:#4ade80;font-weight:700;text-decoration:none;display:inline-block;margin-top:12px}
        .error{display:none;margin-top:16px;background:#7f1d1d;border:1px solid #ef4444;border-radius:8px;padding:14px;font-size:12px;color:#fecaca;white-space:pre-wrap}
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <h1>WhatsApp<span>BizAI</span></h1>
        <p>Installation automatique</p>
    </div>
    <div class="info">
        Étape 1/2 : Installation des <strong>dépendances PHP</strong> via Composer.<br>
        Cette opération peut prendre 1-2 minutes.
    </div>
    <button class="btn" id="btn" onclick="runInstall()">🚀 Lancer l'installation</button>
    <div class="progress" id="progress">
        <div class="bar"><div class="bar-fill" id="bar-fill"></div></div>
        <div class="bar-text" id="bar-text">En cours...</div>
    </div>
    <div class="log" id="log"></div>
    <div class="error" id="error"></div>
    <div class="success" id="success">
        <h2>✅ Dépendances installées !</h2>
        <p>Redirection vers l'assistant d'installation...</p>
        <a href="<?php echo $publicUrl; ?>/install">Cliquez ici si la redirection ne fonctionne pas →</a>
    </div>
</div>
<script>
function runInstall(){
    var btn=document.getElementById('btn');
    var prog=document.getElementById('progress');
    var fill=document.getElementById('bar-fill');
    var txt=document.getElementById('bar-text');
    var log=document.getElementById('log');
    var err=document.getElementById('error');
    var suc=document.getElementById('success');
    btn.disabled=true;btn.textContent='⏳ Installation en cours...';
    prog.style.display='block';err.style.display='none';
    fill.style.width='40%';txt.textContent='Exécution de composer install...';
    log.style.display='block';log.textContent='> Démarrage de composer install --no-dev --optimize-autoloader\n';
    var x=new XMLHttpRequest();
    x.open('POST','',true);
    x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    x.onload=function(){
        try{
            var d=JSON.parse(x.responseText);
            log.textContent+=d.msg+'\n';
            if(d.ok){
                fill.style.width='100%';txt.textContent='Terminé !';
                suc.style.display='block';btn.style.display='none';
                setTimeout(function(){window.location='<?php echo $publicUrl; ?>/install';},2000);
            }else{
                fill.style.width='0%';txt.textContent='Erreur';
                err.textContent='❌ Erreur (code '+d.code+') :\n\n'+d.msg;
                err.style.display='block';
                btn.disabled=false;btn.textContent='🔄 Réessayer';
            }
        }catch(e){
            err.textContent='Erreur de communication : '+e.message;
            err.style.display='block';
            btn.disabled=false;btn.textContent='🔄 Réessayer';
        }
    };
    x.onerror=function(){
        err.textContent='Erreur réseau. Vérifiez qu\'Apache est démarré.';
        err.style.display='block';
        btn.disabled=false;btn.textContent='🔄 Réessayer';
    };
    x.send('install_action=composer_install');
}
</script>
</body>
</html>
<?php
    exit;
}

/*
|--------------------------------------------------------------------------
| B) vendor exists but app not installed → redirect to /install
|--------------------------------------------------------------------------
*/
require __DIR__ . '/vendor/autoload.php';

$envFile    = __DIR__ . '/.env';
$envExample = __DIR__ . '/.env.example';

if (!file_exists($envFile) && file_exists($envExample)) {
    $key = 'base64:' . base64_encode(random_bytes(32));
    $url = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/testwebsite/whatsappbizai-main/public';
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

$installed = false;
if (file_exists($envFile)) {
    $content = file_get_contents($envFile);
    $installed = (bool) preg_match('/^APP_INSTALLED=true$/m', $content);
}

if (!$installed) {
    header('Location: /testwebsite/whatsappbizai-main/public/install');
    exit;
}

/*
|--------------------------------------------------------------------------
| C) Installed → redirect to public/
|--------------------------------------------------------------------------
*/
header('Location: /testwebsite/whatsappbizai-main/public/');
exit;
