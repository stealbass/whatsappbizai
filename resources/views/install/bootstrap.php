<?php
/**
 * WhatsAppBizAI — Pre-install Bootstrap
 * Runs BEFORE Laravel is available (no vendor/autoload.php yet).
 * Handles: composer install → redirect to /install
 */

$basePath = dirname(__DIR__, 2);
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$action = $_POST['action'] ?? $_GET['action'] ?? null;

/*
|--------------------------------------------------------------------------
| AJAX: Run composer install
|--------------------------------------------------------------------------
*/
if ($isAjax && $action === 'composer_install') {
    header('Content-Type: application/json');

    // Find PHP binary
    $phpBin = PHP_BINARY;
    if (!$phpBin || !file_exists($phpBin)) {
        $candidates = [
            'php',
            'C:\\xampp\\php\\php.exe',
            'C:\\php\\php.exe',
            '/usr/bin/php',
            '/usr/local/bin/php',
        ];
        foreach ($candidates as $c) {
            $output = [];
            $exitCode = 0;
            exec("{$c} -v 2>&1", $output, $exitCode);
            if ($exitCode === 0) {
                $phpBin = $c;
                break;
            }
        }
    }

    // Find composer
    $composer = null;
    $candidates = [
        'composer',
        'composer.phar',
        $basePath . '\\composer.phar',
        $basePath . '/composer.phar',
    ];
    foreach ($candidates as $c) {
        $output = [];
        $exitCode = 0;
        exec("{$c} --version 2>&1", $output, $exitCode);
        if ($exitCode === 0) {
            $composer = $c;
            break;
        }
    }

    if (!$composer) {
        echo json_encode(['success' => false, 'message' => 'Composer introuvable. Installez-le depuis https://getcomposer.org']);
        exit;
    }

    // Run composer install
    $cmd = "cd \"{$basePath}\" && {$composer} install --no-dev --optimize-autoloader --no-interaction 2>&1";
    $output = [];
    $exitCode = 0;
    exec($cmd, $output, $exitCode);

    if ($exitCode === 0) {
        echo json_encode(['success' => true, 'message' => 'Dépendances installées avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => "Erreur Composer (code {$exitCode}):\n" . implode("\n", $output)]);
    }
    exit;
}

/*
|--------------------------------------------------------------------------
| HTML: Bootstrap page
|--------------------------------------------------------------------------
*/
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsAppBizAI — Installation</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f172a; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #1e293b; border-radius: 16px; padding: 48px; width: 100%; max-width: 560px; box-shadow: 0 25px 60px rgba(0,0,0,.5); }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo h1 { font-size: 28px; font-weight: 800; }
        .logo h1 span { color: #0ea5e9; }
        .logo p { color: #94a3b8; font-size: 14px; margin-top: 8px; }
        .step { display: flex; align-items: flex-start; gap: 16px; padding: 16px 0; border-bottom: 1px solid #334155; }
        .step:last-child { border-bottom: none; }
        .step-icon { font-size: 20px; flex-shrink: 0; width: 32px; text-align: center; }
        .step-text h3 { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
        .step-text p { font-size: 13px; color: #94a3b8; line-height: 1.5; }
        .btn { display: block; width: 100%; padding: 16px; background: #0ea5e9; color: #fff; border: none; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 24px; transition: background .2s; }
        .btn:hover { background: #0284c7; }
        .btn:disabled { background: #334155; color: #64748b; cursor: not-allowed; }
        .progress-wrap { display: none; margin-top: 24px; }
        .progress-bar { width: 100%; height: 8px; background: #334155; border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #0ea5e9, #22c55e); width: 0%; transition: width .4s; border-radius: 4px; }
        .progress-text { font-size: 13px; color: #94a3b8; margin-top: 8px; text-align: center; }
        .log { display: none; margin-top: 16px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 16px; font-family: 'Fira Code', 'Consolas', monospace; font-size: 12px; color: #94a3b8; max-height: 200px; overflow-y: auto; white-space: pre-wrap; word-break: break-all; }
        .success-box { display: none; margin-top: 24px; background: #166534; border: 1px solid #22c55e; border-radius: 10px; padding: 24px; text-align: center; }
        .success-box h2 { font-size: 20px; margin-bottom: 8px; }
        .success-box p { font-size: 14px; color: #bbf7d0; margin-bottom: 4px; }
        .success-box a { color: #4ade80; font-weight: 700; text-decoration: none; }
        .success-box .creds { background: #0f172a; border-radius: 8px; padding: 16px; margin-top: 16px; text-align: left; font-family: monospace; font-size: 13px; }
        .success-box .creds div { margin-bottom: 4px; }
        .success-box .creds span { color: #0ea5e9; }
        .error-msg { display: none; margin-top: 16px; background: #7f1d1d; border: 1px solid #ef4444; border-radius: 8px; padding: 16px; font-size: 13px; color: #fecaca; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <h1>WhatsApp<span>BizAI</span></h1>
        <p>Installation automatique</p>
    </div>

    <div class="step">
        <div class="step-icon">📦</div>
        <div class="step-text">
            <h3>Installation des dépendances</h3>
            <p>Les包 PHP nécessaires au fonctionnement de l'application vont être installées via Composer.</p>
        </div>
    </div>

    <button class="btn" id="btn-install" onclick="startInstall()">🚀 Lancer l'installation</button>

    <div class="progress-wrap" id="progress-wrap">
        <div class="progress-bar"><div class="progress-fill" id="progress-fill"></div></div>
        <div class="progress-text" id="progress-text">Préparation...</div>
    </div>

    <div class="log" id="log"></div>
    <div class="error-msg" id="error-msg"></div>

    <div class="success-box" id="success-box">
        <h2>✅ Installation des dépendances terminée !</h2>
        <p>Redirection vers l'assistant d'installation...</p>
        <p style="margin-top:12px"><a href="/install">Cliquer ici si la redirection ne démarre pas →</a></p>
    </div>
</div>

<script>
function startInstall() {
    var btn = document.getElementById('btn-install');
    var progressWrap = document.getElementById('progress-wrap');
    var progressFill = document.getElementById('progress-fill');
    var progressText = document.getElementById('progress-text');
    var log = document.getElementById('log');
    var errorMsg = document.getElementById('error-msg');
    var successBox = document.getElementById('success-box');

    btn.disabled = true;
    btn.textContent = '⏳ Installation en cours...';
    progressWrap.style.display = 'block';
    errorMsg.style.display = 'none';

    progressFill.style.width = '30%';
    progressText.textContent = 'Exécution de composer install...';
    log.textContent += '> Exécution de composer install --no-dev --optimize-autoloader\n';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', window.location.pathname + '?action=composer_install', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onload = function() {
        try {
            var resp = JSON.parse(xhr.responseText);
            log.textContent += resp.message + '\n';

            if (resp.success) {
                progressFill.style.width = '100%';
                progressText.textContent = 'Terminé !';
                successBox.style.display = 'block';
                btn.style.display = 'none';

                // Redirect to /install after 2 seconds
                setTimeout(function() {
                    window.location.href = '/install';
                }, 2000);
            } else {
                progressFill.style.width = '0%';
                progressText.textContent = 'Erreur';
                errorMsg.textContent = resp.message;
                errorMsg.style.display = 'block';
                btn.disabled = false;
                btn.textContent = '🔄 Réessayer';
            }
        } catch(e) {
            log.textContent += 'Erreur de réponse du serveur.\n';
            errorMsg.textContent = 'Erreur de communication avec le serveur.';
            errorMsg.style.display = 'block';
            btn.disabled = false;
            btn.textContent = '🔄 Réessayer';
        }
    };

    xhr.onerror = function() {
        errorMsg.textContent = 'Erreur réseau. Vérifiez que le serveur Apache est démarré.';
        errorMsg.style.display = 'block';
        btn.disabled = false;
        btn.textContent = '🔄 Réessayer';
    };

    xhr.send('action=composer_install');
}
</script>
</body>
</html>
