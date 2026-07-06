<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.import.title') }} — WhatsAppBizAI</title>
    <meta name="robots" content="noindex">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#0f172a;color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .card{background:#1e293b;border-radius:16px;padding:40px;width:100%;max-width:620px}
        h1{font-size:22px;font-weight:800;margin-bottom:6px}
        .sub{color:#94a3b8;font-size:14px;margin-bottom:28px}
        .zone{border:2px dashed #334155;border-radius:10px;padding:32px;text-align:center;cursor:pointer;transition:border .2s;margin-bottom:18px}
        .zone:hover,.zone.drag{border-color:#0ea5e9;background:#0ea5e910}
        .zone-icon{font-size:36px;margin-bottom:10px}
        .zone p{color:#94a3b8;font-size:14px}
        .zone strong{color:#fff}
        input[type=file]{display:none}
        .btn{display:inline-block;padding:12px 28px;background:#0ea5e9;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none}
        .btn-gray{background:#334155}
        .btn-full{width:100%;text-align:center;margin-top:8px}
        .alert-success{background:#166534;border:1px solid #16a34a;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px}
        .alert-warning{background:#7c2d12;border:1px solid #f97316;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px}
        .stat-row{display:flex;gap:12px;margin-bottom:16px}
        .stat{flex:1;background:#0f172a;border-radius:10px;padding:16px;text-align:center}
        .stat-n{font-size:28px;font-weight:900;color:#22c55e}
        .stat-n.skip{color:#f59e0b}
        .stat-n.err{color:#ef4444}
        .stat-label{font-size:12px;color:#94a3b8;margin-top:4px}
        .errors{background:#0f172a;border-radius:8px;padding:12px;margin-top:12px;max-height:120px;overflow-y:auto}
        .errors li{font-size:12px;color:#fca5a5;margin-bottom:4px;list-style:disc;margin-left:16px}
        .format-box{background:#0f172a;border-radius:10px;padding:16px;margin:20px 0;font-size:13px}
        .format-box h3{font-size:13px;font-weight:700;margin-bottom:8px;color:#0ea5e9}
        table.fmt{width:100%;border-collapse:collapse;font-size:12px}
        table.fmt th{text-align:left;color:#64748b;padding:4px 8px;border-bottom:1px solid #1e293b}
        table.fmt td{padding:4px 8px;color:#cbd5e1;border-bottom:1px solid #1e293b0a}
        .back{display:block;margin-top:16px;color:#0ea5e9;font-size:13px;text-decoration:none}
    </style>
</head>
<body>
<div class="card">
    <h1>{{ __('app.import.title') }}</h1>
    <p class="sub">{{ __('app.import.desc') }}</p>

    @if(session('import_results'))
        @php $r = session('import_results') @endphp
        <div class="stat-row">
            <div class="stat"><div class="stat-n">{{ $r['imported'] }}</div><div class="stat-label">{{ __('app.import.imported') }}</div></div>
            <div class="stat"><div class="stat-n skip">{{ $r['skipped'] }}</div><div class="stat-label">{{ __('app.import.skipped') }}</div></div>
            <div class="stat"><div class="stat-n err">{{ count($r['errors']) }}</div><div class="stat-label">{{ __('app.import.errors') }}</div></div>
        </div>
        @if(count($r['errors']))
            <div class="alert-warning">⚠️ {{ __('app.import.some_skipped') }}
                <ul class="errors">
                    @foreach(array_slice($r['errors'], 0, 10) as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                    @if(count($r['errors']) > 10)
                        <li>{{ __('app.import.more_errors', ['count' => count($r['errors']) - 10]) }}</li>
                    @endif
                </ul>
            </div>
        @else
            <div class="alert-success">✅ {{ __('app.import.completed', ['count' => $r['imported']]) }}</div>
        @endif
    @endif

    <form method="POST" action="{{ route('import.contacts') }}" enctype="multipart/form-data" id="import-form">
        @csrf
        <div class="zone" id="drop-zone" onclick="document.getElementById('csv-input').click()">
            <div class="zone-icon">📄</div>
            <strong id="file-label">{{ __('app.import.select_file') }}</strong>
            <p>{{ __('app.import.columns_hint') }}</p>
        </div>
        <input type="file" name="csv_file" id="csv-input" accept=".csv,.txt" onchange="onFileSelected(this)">

        @error('csv_file')
            <p style="color:#ef4444;font-size:13px;margin-bottom:10px">{{ $message }}</p>
        @enderror

        <button type="submit" class="btn btn-full">⬆️ {{ __('app.import.import') }}</button>
    </form>

    <div class="format-box">
        <h3>📋 {{ __('app.import.format_title') }}</h3>
        <table class="fmt">
            <tr><th>{{ __('app.import.column') }}</th><th>{{ __('app.import.required') }}</th><th>{{ __('app.import.accepted_values') }}</th></tr>
            <tr><td>whatsapp_number</td><td style="color:#22c55e">{{ __('app.import.yes') }}</td><td>+237600000001, 00237600000001</td></tr>
            <tr><td>name / {{ __('app.client.contact_name') }}</td><td>{{ __('app.import.no') }}</td><td>{{ __('app.import.free_text') }}</td></tr>
            <tr><td>email</td><td>{{ __('app.import.no') }}</td><td>{{ __('app.import.valid_email') }}</td></tr>
            <tr><td>company / {{ __('app.admin.company_name') }}</td><td>{{ __('app.import.no') }}</td><td>{{ __('app.import.free_text') }}</td></tr>
            <tr><td>status / {{ __('app.admin.status') }}</td><td>{{ __('app.import.no') }}</td><td>prospect, client, inactif</td></tr>
            <tr><td>tags / {{ __('app.admin.tags') }}</td><td>{{ __('app.import.no') }}</td><td>{{ __('app.import.tags_hint') }}</td></tr>
            <tr><td>notes</td><td>{{ __('app.import.no') }}</td><td>{{ __('app.import.free_text') }}</td></tr>
        </table>
        <p style="margin-top:10px;color:#64748b;font-size:12px">💡 {{ __('app.import.order_hint') }}</p>
    </div>

    <a href="{{ route('import.template') }}" class="btn btn-gray" style="display:block;text-align:center">
        ⬇️ {{ __('app.import.download_template') }}
    </a>
    <a href="/admin/contacts" class="back">{{ __('app.import.back') }}</a>
</div>

<script>
function onFileSelected(input) {
    const label = document.getElementById('file-label');
    label.textContent = input.files[0] ? '✅ ' + input.files[0].name : '{{ __("app.import.select_file") }}';
}
const zone = document.getElementById('drop-zone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag'); });
zone.addEventListener('dragleave', () => zone.classList.remove('drag'));
zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('drag');
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer(); dt.items.add(file);
        document.getElementById('csv-input').files = dt.files;
        onFileSelected(document.getElementById('csv-input'));
    }
});
</script>
</body>
</html>
