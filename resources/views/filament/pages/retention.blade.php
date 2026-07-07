<x-filament-panels::page>
    <form wire:submit="sendCampaign" id="retention-form">
        {{ $this->form }}

        <div class="mt-6 flex flex-wrap justify-end gap-3">
            {{-- Draft AI --}}
            <x-filament::button
                type="button"
                color="info"
                icon="heroicon-o-sparkles"
                wire:click="draftWithAI"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="draftWithAI">{{ __('app.client.retention.draft_ai') }}</span>
                <span wire:loading wire:target="draftWithAI">{{ __('app.client.retention.sending') }}</span>
            </x-filament::button>

            {{-- Preview --}}
            <x-filament::button
                type="button"
                color="warning"
                icon="heroicon-o-eye"
                wire:click="$set('showPreview', true)"
            >
                👁️ {{ __('Aperçu') }}
            </x-filament::button>

            <x-filament::button
                type="submit"
                color="success"
                icon="heroicon-o-paper-airplane"
                wire:confirm="{{ __('app.client.retention.send_confirm') }}"
            >
                {{ __('app.client.retention.send') }}
            </x-filament::button>
        </div>
    </form>

    @if($showPreview)
    {{-- Hidden element to pass data to JS --}}
    <div wire:ignore style="display:none;" id="retention-preview-data">{!! $previewHtml !!}</div>

    {{-- Preview Modal --}}
    <div
        class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto"
        style="background:rgba(0,0,0,.55); padding: 40px 16px;"
        wire:click.self="$set('showPreview', false)"
    >
        <div style="width:100%;max-width:720px;background:#fff;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;">

            {{-- Header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <span style="font-weight:700;font-size:15px;">👁️ Aperçu de l'email</span>
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="font-size:11px;color:#64748b;background:#e2e8f0;padding:3px 10px;border-radius:99px;">
                        Rendu réel reçu par le destinataire
                    </span>
                    <button
                        type="button"
                        wire:click="$set('showPreview', false)"
                        style="background:none;border:none;cursor:pointer;font-size:22px;color:#94a3b8;line-height:1;"
                    >&times;</button>
                </div>
            </div>

            {{-- Envelope info --}}
            <div style="padding:12px 20px;background:#e9ecef;border-bottom:1px solid #dee2e6;font-size:12px;color:#6c757d;font-family:monospace;">
                <div><strong>De :</strong> {{ config('mail.from.name', 'WhatsAppBizAI') }} &lt;{{ config('mail.from.address', 'noreply@example.com') }}&gt;</div>
                <div><strong>Objet :</strong> {{ __('app.admin.retention_subject') }}</div>
            </div>

            {{-- Iframe render --}}
            <div style="padding:20px;">
                <iframe
                    id="retention-preview-iframe"
                    sandbox="allow-same-origin"
                    style="width:100%;min-height:400px;border:1px solid #e2e8f0;border-radius:8px;"
                    title="Aperçu email"
                ></iframe>
            </div>

            {{-- Footer --}}
            <div style="padding:16px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                <span style="font-size:12px;color:#64748b;">
                    Si le HTML contient <code>&lt;!DOCTYPE html&gt;</code>, il est rendu tel quel.
                </span>
                <div style="display:flex;gap:8px;">
                    <x-filament::button type="button" color="gray" wire:click="$set('showPreview', false)">
                        Fermer
                    </x-filament::button>
                    <x-filament::button
                        type="button"
                        color="success"
                        icon="heroicon-o-paper-airplane"
                        wire:click="sendCampaign"
                        wire:confirm="{{ __('app.client.retention.send_confirm') }}"
                    >
                        📤 Envoyer maintenant
                    </x-filament::button>
                </div>
            </div>

        </div>
    </div>
    @endif

    @if($showPreview)
    <script>
        (function() {
            var dataEl = document.getElementById('retention-preview-data');
            var iframe = document.getElementById('retention-preview-iframe');
            if (!dataEl || !iframe) return;
            var raw = dataEl.innerHTML;
            var isFullDoc = /^\s*<!DOCTYPE/i.test(raw) || /^\s*<html/i.test(raw);
            var content = isFullDoc ? raw : '<!DOCTYPE html><html><head>' +
                '<meta charset="utf-8">' +
                '<style>' +
                    'body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;' +
                          'font-size:14px;line-height:1.7;color:#1e293b;' +
                          'max-width:600px;margin:0 auto;padding:24px;}' +
                    'h1,h2,h3{color:#0f172a;}' +
                    'a{color:#0ea5e9;}' +
                    'blockquote{border-left:3px solid #e2e8f0;margin-left:0;padding-left:16px;color:#64748b;}' +
                    'pre,code{background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:13px;}' +
                    'ul,ol{padding-left:24px;}img{max-width:100%;}' +
                '</style>' +
                '</head><body>' + raw + '</body></html>';
            iframe.srcdoc = content;
            iframe.onload = function() {
                try {
                    var h = iframe.contentDocument.body.scrollHeight;
                    iframe.style.height = Math.max(400, h + 40) + 'px';
                } catch(e) {}
            };
        })();
    </script>
    @endif

</x-filament-panels::page>
