<x-filament-panels::page>
    <form wire:submit="sendCampaign" id="retention-form">
        {{ $this->form }}

        <div class="mt-6 flex flex-wrap justify-end gap-3">
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

            <x-filament::button
                type="button"
                color="warning"
                icon="heroicon-o-eye"
                wire:click="previewContent"
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
    {{-- Store raw HTML as base64 so Blade encoding doesn't corrupt it --}}
    <script type="text/html" id="retention-data">{!! base64_encode($previewHtml) !!}</script>

    <div
        class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto"
        style="background:rgba(0,0,0,.55); padding: 40px 16px;"
        wire:click.self="$set('showPreview', false)"
    >
        <div style="width:100%;max-width:720px;background:#fff;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <span style="font-weight:700;font-size:15px;">👁️ Aperçu de l'email</span>
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="font-size:11px;color:#64748b;background:#e2e8f0;padding:3px 10px;border-radius:99px;">Rendu réel reçu par le destinataire</span>
                    <button type="button" wire:click="$set('showPreview', false)" style="background:none;border:none;cursor:pointer;font-size:22px;color:#94a3b8;line-height:1;">&times;</button>
                </div>
            </div>
            <div style="padding:12px 20px;background:#e9ecef;border-bottom:1px solid #dee2e6;font-size:12px;color:#6c757d;font-family:monospace;">
                <div><strong>De :</strong> {{ config('mail.from.name', 'WhatsAppBizAI') }} &lt;{{ config('mail.from.address', 'noreply@example.com') }}&gt;</div>
                <div><strong>Objet :</strong> {{ __('app.admin.retention_subject') }}</div>
            </div>
            <div style="padding:20px;">
                <iframe id="retention-preview-iframe" style="width:100%;min-height:400px;border:1px solid #e2e8f0;border-radius:8px;" title="Aperçu email"></iframe>
            </div>
            <div style="padding:16px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                <span style="font-size:12px;color:#64748b;">Si le HTML contient <code>&lt;!DOCTYPE html&gt;</code>, il est rendu tel quel.</span>
                <div style="display:flex;gap:8px;">
                    <x-filament::button type="button" color="gray" wire:click="$set('showPreview', false)">Fermer</x-filament::button>
                    <x-filament::button type="button" color="success" icon="heroicon-o-paper-airplane" wire:click="sendCampaign" wire:confirm="{{ __('app.client.retention.send_confirm') }}">📤 Envoyer maintenant</x-filament::button>
                </div>
            </div>
        </div>
    </div>
    @endif

</x-filament-panels::page>

@script
<script>
    $wire.on('previewContent', () => {
        setTimeout(() => {
            var el = document.getElementById('retention-data');
            var iframe = document.getElementById('retention-preview-iframe');
            if (el && iframe) {
                var raw = atob(el.textContent.trim());
                iframe.srcdoc = raw;
                iframe.onload = function() {
                    try { iframe.style.height = Math.max(400, iframe.contentDocument.body.scrollHeight + 40) + 'px'; } catch(e) {}
                };
            }
        }, 50);
    });
</script>
@endscript
