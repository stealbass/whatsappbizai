<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsAppBizAI — Assistant d'installation</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f172a; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #1e293b; border-radius: 16px; padding: 40px; width: 100%; max-width: 620px; box-shadow: 0 25px 60px rgba(0,0,0,.5); }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo h1 { font-size: 26px; font-weight: 800; }
        .logo h1 span { color: #0ea5e9; }
        .logo p { color: #94a3b8; font-size: 14px; margin-top: 6px; }

        /* Steps indicator */
        .steps-indicator { display: flex; justify-content: center; gap: 0; margin-bottom: 32px; }
        .step-dot { width: 36px; height: 36px; border-radius: 50%; background: #334155; color: #64748b; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; transition: all .3s; position: relative; }
        .step-dot.active { background: #0ea5e9; color: #fff; }
        .step-dot.done { background: #22c55e; color: #fff; }
        .step-dot.error { background: #ef4444; color: #fff; }
        .step-line { width: 40px; height: 2px; background: #334155; align-self: center; }
        .step-line.active { background: #0ea5e9; }

        /* Sections */
        .section { display: none; }
        .section.active { display: block; }
        .section h2 { font-size: 20px; font-weight: 800; margin-bottom: 6px; }
        .section .subtitle { color: #94a3b8; font-size: 14px; margin-bottom: 24px; }

        /* Requirement checks */
        .check-list { list-style: none; }
        .check-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #334155; font-size: 14px; }
        .check-item:last-child { border-bottom: none; }
        .check-icon { width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0; }
        .check-icon.ok { background: #166534; color: #4ade80; }
        .check-icon.fail { background: #7f1d1d; color: #fca5a5; }
        .check-icon.pending { background: #334155; color: #64748b; }
        .check-detail { color: #64748b; font-size: 12px; margin-left: auto; }

        /* Forms */
        label { display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; margin-top: 14px; }
        input, select { width: 100%; padding: 11px 14px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff; font-size: 14px; outline: none; transition: border .2s; }
        input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,.1); }
        .row { display: grid; grid-template-columns: 2fr 1fr; gap: 14px; }
        .hint { font-size: 11px; color: #64748b; margin-top: 4px; }
        .optional-badge { display: inline-block; background: #334155; color: #94a3b8; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 10px; margin-left: 8px; }
        .divider-text { text-align: center; color: #475569; font-size: 12px; margin: 20px 0; border-top: 1px solid #334155; padding-top: 16px; }

        /* Buttons */
        .btn { display: block; width: 100%; padding: 14px; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; margin-top: 20px; transition: opacity .2s; }
        .btn:hover { opacity: .85; }
        .btn-primary { background: #0ea5e9; color: #fff; }
        .btn-success { background: #22c55e; color: #fff; }
        .btn-secondary { background: #334155; color: #94a3b8; }
        .btn:disabled { background: #334155; color: #64748b; cursor: not-allowed; }

        /* Progress */
        .install-progress { margin-top: 20px; }
        .progress-step { display: flex; align-items: center; gap: 12px; padding: 8px 0; font-size: 13px; }
        .progress-step .icon { width: 20px; text-align: center; flex-shrink: 0; }
        .progress-step .label { color: #94a3b8; }
        .progress-step.active .label { color: #fff; }
        .progress-step.done .label { color: #4ade80; }
        .progress-step.error .label { color: #fca5a5; }

        /* Success */
        .success-box { text-align: center; padding: 20px 0; }
        .success-icon { font-size: 48px; margin-bottom: 16px; }
        .success-box h2 { font-size: 22px; margin-bottom: 8px; color: #4ade80; }
        .success-box p { color: #94a3b8; font-size: 14px; margin-bottom: 4px; }
        .creds { background: #0f172a; border: 1px solid #334155; border-radius: 10px; padding: 20px; margin-top: 24px; text-align: left; }
        .creds h3 { font-size: 14px; font-weight: 700; margin-bottom: 12px; color: #0ea5e9; }
        .creds div { font-family: 'Fira Code', Consolas, monospace; font-size: 13px; margin-bottom: 6px; color: #cbd5e1; }
        .creds span { color: #0ea5e9; }

        .btn-admin { display: inline-block; margin-top: 24px; padding: 14px 32px; background: #0ea5e9; color: #fff; border-radius: 10px; font-size: 16px; font-weight: 700; text-decoration: none; transition: background .2s; }
        .btn-admin:hover { background: #0284c7; }

        @media(max-width:600px) { .card { padding: 24px; } .row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <h1>WhatsApp<span>BizAI</span></h1>
        <p>Assistant d'installation automatique</p>
    </div>

    <!-- Steps indicator -->
    <div class="steps-indicator">
        <div class="step-dot active" id="dot-1">1</div>
        <div class="step-line" id="line-1"></div>
        <div class="step-dot" id="dot-2">2</div>
        <div class="step-line" id="line-2"></div>
        <div class="step-dot" id="dot-3">3</div>
        <div class="step-line" id="line-3"></div>
        <div class="step-dot" id="dot-4">4</div>
        <div class="step-line" id="line-4"></div>
        <div class="step-dot" id="dot-5">5</div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- STEP 1: Requirements -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="section active" id="step-1">
        <h2>1. Vérification des prérequis</h2>
        <p class="subtitle">Vérification automatique de l'environnement serveur.</p>
        <ul class="check-list" id="req-list">
            <li class="check-item"><div class="check-icon pending">…</div> Vérification en cours...</li>
        </ul>
        <button class="btn btn-primary" id="btn-step1" onclick="nextStep(2)" disabled>Vérifier et continuer →</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- STEP 2: Database -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="section" id="step-2">
        <h2>2. Configuration de la base de données</h2>
        <p class="subtitle">Entrez les informations de connexion MySQL de votre serveur XAMPP.</p>

        <label>Nom de l'application</label>
        <input type="text" id="app_name" value="WhatsAppBizAI">

        <label>URL de l'application</label>
        <input type="text" id="app_url" value="{{ url('/') }}">
        <div class="hint">URL complète avec protocole (ex: http://localhost/testwebsite/whatsappbizai-main)</div>

        <label>Hôte MySQL</label>
        <input type="text" id="db_host" value="127.0.0.1">

        <div class="row">
            <div>
                <label>Port</label>
                <input type="number" id="db_port" value="3306">
            </div>
            <div>
                <label>Base de données</label>
                <input type="text" id="db_name" value="whatsappbizai">
            </div>
        </div>

        <label>Utilisateur</label>
        <input type="text" id="db_username" value="root">

        <label>Mot de passe</label>
        <input type="password" id="db_password" value="" placeholder="Laisser vide si pas de mot de passe">

        <button class="btn btn-secondary" onclick="testDb()" id="btn-test-db" style="margin-top:12px">🔌 Tester la connexion</button>
        <div id="db-test-result" style="margin-top:8px;font-size:13px;"></div>

        <button class="btn btn-primary" id="btn-step2" onclick="nextStep(3)">Continuer →</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- STEP 3: API Config (optional) -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="section" id="step-3">
        <h2>3. Configuration des API <span class="optional-badge">Optionnel</span></h2>
        <p class="subtitle">Vous pouvez configurer ces clés plus tard dans le fichier .env.</p>

        <div class="divider-text">WhatsApp Cloud API (Meta)</div>
        <label>Phone Number ID</label>
        <input type="text" id="whatsapp_phone_number_id" placeholder="Ex: 1234567890">
        <label>Access Token</label>
        <input type="text" id="whatsapp_access_token" placeholder="Token d'accès WhatsApp">
        <label>Verify Token</label>
        <input type="text" id="whatsapp_verify_token" value="your_custom_verify_token_here">
        <label>Business Account ID</label>
        <input type="text" id="whatsapp_business_account_id" placeholder="ID du compte WhatsApp Business">

        <div class="divider-text">Gemini AI (Google)</div>
        <label>Clé API Gemini</label>
        <input type="text" id="gemini_api_key" placeholder="AIza...">

        <div class="divider-text">Flutterwave (Paiements)</div>
        <label>Clé publique Flutterwave</label>
        <input type="text" id="flutterwave_public_key" placeholder="FLWPUBK_TEST-...">
        <label>Clé secrète Flutterwave</label>
        <input type="password" id="flutterwave_secret_key" placeholder="FLWSECK_TEST-...">
        <label>Webhook Secret</label>
        <input type="text" id="flutterwave_webhook_secret" placeholder="Secret du webhook">

        <button class="btn btn-primary" onclick="nextStep(4)">Lancer l'installation →</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- STEP 4: Installation in progress -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="section" id="step-4">
        <h2>4. Installation en cours...</h2>
        <p class="subtitle">Veuillez patienter, ne fermez pas cette page.</p>
        <div class="install-progress" id="install-progress">
            <div class="progress-step" id="ps-1"><div class="icon">⏳</div><div class="label">Création du fichier .env...</div></div>
            <div class="progress-step" id="ps-2"><div class="icon">⏳</div><div class="label">Vidage du cache...</div></div>
            <div class="progress-step" id="ps-3"><div class="icon">⏳</div><div class="label">Génération de la clé APP_KEY...</div></div>
            <div class="progress-step" id="ps-4"><div class="icon">⏳</div><div class="label">Exécution des migrations (11 tables)...</div></div>
            <div class="progress-step" id="ps-5"><div class="icon">⏳</div><div class="label">Chargement des données de démo...</div></div>
            <div class="progress-step" id="ps-6"><div class="icon">⏳</div><div class="label">Création du lien storage...</div></div>
            <div class="progress-step" id="ps-7"><div class="icon">⏳</div><div class="label">Marquage installation terminée...</div></div>
            <div class="progress-step" id="ps-8"><div class="icon">⏳</div><div class="label">Optimisation du cache...</div></div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- STEP 5: Done -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="section" id="step-5">
        <div class="success-box">
            <div class="success-icon">🎉</div>
            <h2>Installation terminée !</h2>
            <p>WhatsAppBizAI est prêt à être utilisé.</p>

            <div class="creds">
                <h3>🔑 Admin Credentials démo</h3>
                <div>Email : <span>admin@whatsappbizai.com</span></div>
                <div>Password : <span>password</span></div>
            </div>

            <a href="/admin" class="btn-admin" id="btn-goto-admin">🚀 Accéder au Dashboard</a>
            <div style="margin-top:16px">
                <a href="{{ url('/') }}" style="color:#94a3b8;font-size:13px;text-decoration:none;">← Retour à l'accueil</a>
            </div>
        </div>
    </div>
</div>

<script>
// ─── State ────────────────────────────────────────────────────────
var currentStep = 1;
var dbTested = false;

// ─── Step navigation ──────────────────────────────────────────────
function goToStep(n) {
    currentStep = n;
    document.querySelectorAll('.section').forEach(function(s) { s.classList.remove('active'); });
    document.getElementById('step-' + n).classList.add('active');

    for (var i = 1; i <= 5; i++) {
        var dot = document.getElementById('dot-' + i);
        dot.classList.remove('active', 'done', 'error');
        if (i < n) dot.classList.add('done');
        else if (i === n) dot.classList.add('active');

        if (i < 5) {
            var line = document.getElementById('line-' + i);
            line.classList.toggle('active', i < n);
        }
    }
}

function nextStep(n) {
    if (n === 3 && !dbTested) {
        alert('Testez la connexion à la base de données d\'abord.');
        return;
    }
    if (n === 4) {
        runInstallation();
    }
    goToStep(n);
}

// ─── Step 1: Requirements check ───────────────────────────────────
function checkRequirements() {
    var list = document.getElementById('req-list');
    list.innerHTML = '<li class="check-item"><div class="check-icon pending">…</div> Vérification en cours...</li>';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ url("/install/check") }}', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

    xhr.onload = function() {
        try {
            var data = JSON.parse(xhr.responseText);
            var html = '';
            var checks = data.checks;

            for (var key in checks) {
                var c = checks[key];
                var iconClass = c.ok ? 'ok' : 'fail';
                var iconText = c.ok ? '✓' : '✗';
                var detail = c.detail ? '<span class="check-detail">' + c.detail + '</span>' : '';
                html += '<li class="check-item"><div class="check-icon ' + iconClass + '">' + iconText + '</div> ' + c.label + detail + '</li>';
            }

            list.innerHTML = html;
            var btn = document.getElementById('btn-step1');
            btn.disabled = !data.all_ok;

            if (!data.all_ok) {
                btn.textContent = '❌ Prérequis non satisfaits';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-secondary');
            } else {
                btn.textContent = '✓ Prérequis OK — Continuer →';
            }
        } catch(e) {
            list.innerHTML = '<li class="check-item"><div class="check-icon fail">✗</div> Erreur de communication avec le serveur.</li>';
        }
    };

    xhr.onerror = function() {
        list.innerHTML = '<li class="check-item"><div class="check-icon fail">✗</div> Impossible de contacter le serveur.</li>';
    };

    xhr.send('');
}

// ─── Step 2: Test DB connection ───────────────────────────────────
function testDb() {
    var btn = document.getElementById('btn-test-db');
    var result = document.getElementById('db-test-result');
    btn.disabled = true;
    btn.textContent = '⏳ Test en cours...';
    result.innerHTML = '';
    result.style.color = '#94a3b8';

    var params = [
        'db_host=' + encodeURIComponent(document.getElementById('db_host').value),
        'db_port=' + encodeURIComponent(document.getElementById('db_port').value),
        'db_name=' + encodeURIComponent(document.getElementById('db_name').value),
        'db_username=' + encodeURIComponent(document.getElementById('db_username').value),
        'db_password=' + encodeURIComponent(document.getElementById('db_password').value),
    ].join('&');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ url("/install/test-db") }}', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

    xhr.onload = function() {
        try {
            var data = JSON.parse(xhr.responseText);
            if (data.success) {
                result.innerHTML = '✅ ' + data.message;
                result.style.color = '#4ade80';
                dbTested = true;
            } else {
                result.innerHTML = '❌ ' + data.message;
                result.style.color = '#fca5a5';
                dbTested = false;
            }
        } catch(e) {
            result.innerHTML = '❌ Erreur de communication.';
            result.style.color = '#fca5a5';
        }
        btn.disabled = false;
        btn.textContent = '🔌 Tester la connexion';
    };

    xhr.onerror = function() {
        result.innerHTML = '❌ Erreur réseau.';
        result.style.color = '#fca5a5';
        btn.disabled = false;
        btn.textContent = '🔌 Tester la connexion';
    };

    xhr.send(params);
}

// ─── Step 4: Run installation ─────────────────────────────────────
function runInstallation() {
    goToStep(4);

    var params = [
        'app_name=' + encodeURIComponent(document.getElementById('app_name').value),
        'app_url=' + encodeURIComponent(document.getElementById('app_url').value),
        'db_host=' + encodeURIComponent(document.getElementById('db_host').value),
        'db_port=' + encodeURIComponent(document.getElementById('db_port').value),
        'db_name=' + encodeURIComponent(document.getElementById('db_name').value),
        'db_username=' + encodeURIComponent(document.getElementById('db_username').value),
        'db_password=' + encodeURIComponent(document.getElementById('db_password').value),
        'whatsapp_phone_number_id=' + encodeURIComponent(document.getElementById('whatsapp_phone_number_id').value),
        'whatsapp_access_token=' + encodeURIComponent(document.getElementById('whatsapp_access_token').value),
        'whatsapp_verify_token=' + encodeURIComponent(document.getElementById('whatsapp_verify_token').value),
        'whatsapp_business_account_id=' + encodeURIComponent(document.getElementById('whatsapp_business_account_id').value),
        'gemini_api_key=' + encodeURIComponent(document.getElementById('gemini_api_key').value),
        'flutterwave_public_key=' + encodeURIComponent(document.getElementById('flutterwave_public_key').value),
        'flutterwave_secret_key=' + encodeURIComponent(document.getElementById('flutterwave_secret_key').value),
        'flutterwave_webhook_secret=' + encodeURIComponent(document.getElementById('flutterwave_webhook_secret').value),
    ].join('&');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ url("/install/run") }}', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

    xhr.onload = function() {
        try {
            var data = JSON.parse(xhr.responseText);

            if (data.steps) {
                data.steps.forEach(function(s) {
                    var el = document.getElementById('ps-' + s.step);
                    if (el) {
                        el.classList.remove('active');
                        el.classList.add(s.ok ? 'done' : 'error');
                        el.querySelector('.icon').textContent = s.ok ? '✓' : '✗';
                        if (s.detail) {
                            el.querySelector('.label').textContent += ' (' + s.detail.substring(0, 80) + ')';
                        }
                    }
                });
            }

            if (data.success) {
                setTimeout(function() { goToStep(5); }, 800);
            } else {
                alert('Erreur : ' + (data.message || 'Erreur inconnue'));
            }
        } catch(e) {
            alert('Erreur de communication avec le serveur.');
        }
    };

    xhr.onerror = function() {
        alert('Erreur réseau. Le serveur semble indisponible.');
    };

    xhr.send(params);
}

// ─── Init ─────────────────────────────────────────────────────────
checkRequirements();
</script>
</body>
</html>
