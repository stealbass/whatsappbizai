@extends('client.layout')
@section('title', __('app.client.settings.whatsapp.title'))

@section('content')

{{-- Status badge --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
    <h1 style="font-size:22px;font-weight:800;margin:0;">{{ __('app.client.settings.whatsapp.title') }}</h1>
    @if($business->sandbox_mode)
        <span style="background:#f59e0b;color:#fff;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;">
            {{ __('app.client.settings.whatsapp.sandbox_badge') }}
        </span>
    @else
        <span style="background:#22c55e;color:#fff;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;">
            {{ __('app.client.settings.whatsapp.sandbox_off_badge') }}
        </span>
    @endif
</div>

@if($errors->any())
<div class="alert alert-error" style="margin-bottom:16px;">
    @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════
     SECTION 1 — Sandbox mode
═══════════════════════════════════════════════════════════ --}}
<form action="{{ url('client/settings/whatsapp') }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:20px;">🧪</span>
        <h2 style="margin:0;font-size:16px;">{{ __('app.client.settings.whatsapp.sandbox_mode_label') }}</h2>
    </div>
    <div style="padding:20px;">
        <label style="display:flex;align-items:flex-start;gap:14px;cursor:pointer;">
            <div style="position:relative;flex-shrink:0;margin-top:2px;">
                <input type="checkbox" name="sandbox_mode" id="sandbox_toggle" value="1"
                       {{ $business->sandbox_mode ? 'checked' : '' }}
                       style="width:44px;height:24px;cursor:pointer;accent-color:var(--orange);">
            </div>
            <div>
                <div style="font-weight:600;font-size:14px;">{{ __('app.client.settings.whatsapp.sandbox_mode_label') }}</div>
                <p style="font-size:13px;color:var(--gray);margin:4px 0 0;">{{ __('app.client.settings.whatsapp.sandbox_mode_help') }}</p>
            </div>
        </label>

        {{-- Sandbox messages list --}}
        @if($business->sandbox_mode && $sandboxMessages->count() > 0)
        <div style="margin-top:20px;border-top:1px solid var(--border);padding-top:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <strong style="font-size:13px;">{{ __('app.client.settings.whatsapp.sandbox_messages_title') }} ({{ $sandboxMessages->count() }})</strong>
                <a href="{{ url('client/settings/whatsapp/sandbox/clear') }}"
                   onclick="return confirm('{{ __('app.client.settings.whatsapp.sandbox_clear') }} ?')"
                   style="font-size:12px;color:var(--red);text-decoration:none;">🗑 {{ __('app.client.settings.whatsapp.sandbox_clear') }}</a>
            </div>
            <div style="max-height:280px;overflow-y:auto;display:flex;flex-direction:column;gap:8px;">
                @foreach($sandboxMessages->take(30) as $msg)
                <div style="background:var(--light);border-radius:10px;padding:10px 14px;border-left:3px solid {{ $msg->type === 'document' ? 'var(--sky)' : 'var(--green)' }};">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-size:12px;font-weight:600;color:var(--gray);">
                            {{ $msg->type === 'document' ? '📎' : '💬' }}
                            {{ $msg->contact_name ?? $msg->to }}
                            <span style="font-weight:400;">({{ $msg->to }})</span>
                        </span>
                        <div style="display:flex;align-items:center;gap:8px;">
                            @if($msg->trigger)
                            <span style="font-size:10px;background:#e2e8f0;padding:1px 7px;border-radius:10px;color:var(--gray);">{{ $msg->trigger }}</span>
                            @endif
                            <span style="font-size:11px;color:var(--gray);">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div style="font-size:13px;color:#334155;white-space:pre-line;">{{ $msg->type === 'document' ? '📄 '.$msg->content : Illuminate\Support\Str::limit($msg->content, 200) }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @elseif($business->sandbox_mode)
        <div style="margin-top:16px;padding:14px;background:var(--light);border-radius:8px;text-align:center;font-size:13px;color:var(--gray);">
            {{ __('app.client.settings.whatsapp.sandbox_messages_empty') }}
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECTION 2 — Embedded Signup (connecter WhatsApp en 1 clic)
═══════════════════════════════════════════════════════════ --}}
@if(config('whatsapp.meta_app_id'))
<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:20px;">📲</span>
        <h2 style="margin:0;font-size:16px;">{{ __('app.client.settings.whatsapp.connect_whatsapp') }}</h2>
    </div>
    <div style="padding:20px;">
        <p style="font-size:13px;color:var(--gray);margin-bottom:16px;">
            {{ __('app.client.settings.whatsapp.connect_whatsapp_help') }}
        </p>
        @if($business->whatsapp_phone_number_id && !$business->sandbox_mode)
        <div style="display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
            <span style="font-size:18px;">✅</span>
            <div>
                <div style="font-size:13px;font-weight:600;color:#15803d;">WhatsApp connecté</div>
                <div style="font-size:12px;color:#166534;">Phone ID : {{ $business->whatsapp_phone_number_id }}</div>
            </div>
        </div>
        @endif
        <button type="button" id="whatsapp-signup-btn"
                onclick="launchWhatsAppSignup()"
                style="display:inline-flex;align-items:center;gap:10px;background:#25D366;color:#fff;border:none;border-radius:8px;padding:12px 24px;font-size:14px;font-weight:700;cursor:pointer;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            {{ __('app.client.settings.whatsapp.connect_whatsapp') }}
        </button>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════
     SECTION 3 — Webhook info (lecture seule)
═══════════════════════════════════════════════════════════ --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:20px;">🔗</span>
        <h2 style="margin:0;font-size:16px;">Webhook Meta</h2>
    </div>
    <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
        <div>
            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--gray);display:block;margin-bottom:6px;">
                {{ __('app.client.settings.whatsapp.webhook_url_label') }}
            </label>
            <div style="display:flex;gap:8px;">
                <input type="text" id="webhook_url_field" readonly
                       value="{{ url('api/whatsapp/webhook') }}"
                       style="flex:1;background:var(--light);border:1px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;font-family:monospace;color:var(--gray);">
                <button type="button" onclick="copyField('webhook_url_field', this)"
                        style="padding:10px 16px;background:var(--sky);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;white-space:nowrap;">
                    📋 Copier
                </button>
            </div>
            <p style="font-size:12px;color:var(--gray);margin:6px 0 0;">{{ __('app.client.settings.whatsapp.webhook_url_help') }}</p>
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--gray);display:block;margin-bottom:6px;">
                {{ __('app.client.settings.whatsapp.verify_token_label') }}
            </label>
            <div style="display:flex;gap:8px;">
                <input type="text" id="verify_token_field" readonly
                       value="{{ config('whatsapp.verify_token') }}"
                       style="flex:1;background:var(--light);border:1px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;font-family:monospace;color:var(--gray);">
                <button type="button" onclick="copyField('verify_token_field', this)"
                        style="padding:10px 16px;background:var(--sky);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;white-space:nowrap;">
                    📋 Copier
                </button>
            </div>
            <p style="font-size:12px;color:var(--gray);margin:6px 0 0;">{{ __('app.client.settings.whatsapp.verify_token_help') }}</p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECTION 4 — Credentials manuels (avancé, masqué par défaut)
═══════════════════════════════════════════════════════════ --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="cursor:pointer;display:flex;align-items:center;justify-content:space-between;"
         onclick="toggleAdvanced()">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:20px;">⚙️</span>
            <h2 style="margin:0;font-size:16px;">Configuration manuelle (avancé)</h2>
        </div>
        <span id="advanced-toggle-icon" style="font-size:18px;color:var(--gray);">▼</span>
    </div>
    <div id="advanced-section" style="display:none;padding:20px;border-top:1px solid var(--border);">
        <p style="font-size:13px;color:var(--orange);background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;margin-bottom:20px;">
            ⚠️ Utilisez cette section uniquement si vous configurez WhatsApp manuellement via Meta Business Suite.
            Si vous avez utilisé "Connecter mon WhatsApp" ci-dessus, ces champs sont remplis automatiquement.
        </p>
        <div class="form-group">
            <label>{{ __('app.client.settings.whatsapp.phone_id_label') }}</label>
            <input type="text" name="whatsapp_phone_number_id"
                   value="{{ old('whatsapp_phone_number_id', $business->whatsapp_phone_number_id ?? '') }}"
                   placeholder="123456789012345">
            <p class="form-help">{{ __('app.client.settings.whatsapp.phone_id_help') }}</p>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.settings.whatsapp.token_label') }}</label>
            <input type="password" name="whatsapp_access_token"
                   value="{{ old('whatsapp_access_token', $business->whatsapp_access_token ?? '') }}">
            <p class="form-help">{{ __('app.client.settings.whatsapp.token_help') }}</p>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.settings.whatsapp.account_id_label') }}</label>
            <input type="text" name="whatsapp_business_account_id"
                   value="{{ old('whatsapp_business_account_id', $business->whatsapp_business_account_id ?? '') }}"
                   placeholder="987654321098765">
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECTION 5 — AI config
═══════════════════════════════════════════════════════════ --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:20px;">🤖</span>
        <h2 style="margin:0;font-size:16px;">{{ __('app.client.settings.whatsapp.prompt_label') }}</h2>
    </div>
    <div style="padding:20px;">
        <div class="form-group">
            <textarea name="gemini_system_prompt" id="gemini_prompt" rows="6"
                      placeholder="{{ __('app.client.settings.whatsapp.prompt_placeholder') }}">{!! old('gemini_system_prompt', $business->gemini_system_prompt ?? '') !!}</textarea>
            <p class="form-help">{{ __('app.client.settings.whatsapp.prompt_help') }}</p>
        </div>

        <div class="form-group" style="margin-top:20px;">
            <label>{{ __('app.client.settings.whatsapp.documents_label') }}</label>
            <p class="form-help" style="margin-bottom:12px;">{{ __('app.client.settings.whatsapp.documents_help') }}</p>

            @if(!empty($business->ai_documents) && count($business->ai_documents) > 0)
            <div style="margin-bottom:12px;">
                @foreach($business->ai_documents as $index => $doc)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--light);border-radius:8px;margin-bottom:6px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:18px;">
                            @if(str_contains($doc['type'] ?? '', 'pdf')) 📄
                            @elseif(str_contains($doc['type'] ?? '', 'word') || str_contains($doc['type'] ?? '', 'document')) 📝
                            @elseif(str_contains($doc['type'] ?? '', 'excel') || str_contains($doc['type'] ?? '', 'sheet')) 📊
                            @else 📎
                            @endif
                        </span>
                        <div>
                            <div style="font-size:13px;font-weight:600;">{{ $doc['name'] }}</div>
                            <div style="font-size:11px;color:var(--gray);">{{ round(($doc['size'] ?? 0) / 1024) }} KB</div>
                        </div>
                    </div>
                    <button type="submit" name="delete_document" value="{{ $index }}"
                            class="btn btn-ghost btn-sm" style="color:var(--red);"
                            onclick="return confirm('{{ __('app.client.settings.whatsapp.delete_doc_confirm') }}')">✕</button>
                </div>
                @endforeach
            </div>
            @endif

            <input type="file" name="ai_documents[]" multiple
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.md"
                   style="display:block;width:100%;padding:12px;border:2px dashed var(--border);border-radius:8px;cursor:pointer;font-size:14px;"
                   onchange="this.style.borderColor='var(--sky)'">
            <p class="form-help" style="margin-top:6px;">{{ __('app.client.settings.whatsapp.accepted_formats') }}</p>
        </div>
    </div>
</div>

<div style="margin-top:4px;">
    <button type="submit" class="btn btn-primary">{{ __('app.common.save') }}</button>
</div>

</form>
@endsection

@section('scripts')
@include('components.quill')
<script>
initQuill('#gemini_prompt', 300);

// Copy to clipboard helper
function copyField(fieldId, btn) {
    const val = document.getElementById(fieldId).value;
    navigator.clipboard.writeText(val).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '✅ Copié !';
        btn.style.background = 'var(--green)';
        setTimeout(() => { btn.innerHTML = orig; btn.style.background = 'var(--sky)'; }, 2000);
    });
}

// Toggle advanced manual config
function toggleAdvanced() {
    const sec = document.getElementById('advanced-section');
    const icon = document.getElementById('advanced-toggle-icon');
    if (sec.style.display === 'none') {
        sec.style.display = 'block';
        icon.textContent = '▲';
    } else {
        sec.style.display = 'none';
        icon.textContent = '▼';
    }
}

// Auto-open advanced section if credentials already set (manual config)
@if($business->whatsapp_phone_number_id && !config('whatsapp.meta_app_id'))
toggleAdvanced();
@endif

// ─── Meta Embedded Signup ────────────────────────────────────────────────────
@if(config('whatsapp.meta_app_id'))
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = 'https://connect.facebook.net/en_US/sdk.js';
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

window.fbAsyncInit = function() {
    FB.init({
        appId   : '{{ config('whatsapp.meta_app_id') }}',
        autoLogAppEvents: true,
        xfbml   : true,
        version : '{{ config('whatsapp.api_version', 'v20.0') }}'
    });
};

function launchWhatsAppSignup() {
    FB.login(function(response) {
        if (response.authResponse) {
            const code = response.authResponse.code;
            const btn = document.getElementById('whatsapp-signup-btn');
            btn.innerHTML = '⏳ Connexion en cours...';
            btn.disabled = true;

            fetch('{{ url('client/settings/whatsapp/connect') }}', {
                method : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: code })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML = '✅ WhatsApp connecté !';
                    btn.style.background = 'var(--green)';
                    setTimeout(() => location.reload(), 1500);
                } else {
                    btn.innerHTML = '❌ Erreur — réessayez';
                    btn.style.background = 'var(--red)';
                    btn.disabled = false;
                    setTimeout(() => {
                        btn.innerHTML = '{{ __('app.client.settings.whatsapp.connect_whatsapp') }}';
                        btn.style.background = '#25D366';
                    }, 3000);
                    console.error(data.error);
                }
            })
            .catch(err => {
                btn.innerHTML = '❌ Erreur réseau';
                btn.style.background = 'var(--red)';
                btn.disabled = false;
                setTimeout(() => {
                    btn.innerHTML = '{{ __('app.client.settings.whatsapp.connect_whatsapp') }}';
                    btn.style.background = '#25D366';
                }, 3000);
            });
        }
    }, {
        config_id : '{{ config('whatsapp.meta_config_id') }}',
        response_type: 'code',
        override_default_response_type: true,
        extras: {
            setup: {},
            featureType: '',
            sessionInfoVersion: '2'
        }
    });
}
@endif
</script>
@endsection
