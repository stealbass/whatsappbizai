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
                x-on:click="previewRetention()"
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

    {{-- Preview Modal --}}
<div id="retentionPreviewModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.55);overflow-y:auto;">
    <div style="max-width:680px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <span style="font-weight:700;font-size:15px;">👁️ Aperçu du message</span>
            <button onclick="closeRetentionPreview()" style="background:none;border:none;cursor:pointer;font-size:20px;color:#94a3b8;line-height:1;">✕</button>
        </div>
        <div style="padding:24px;min-height:300px;">
            <iframe id="retentionPreviewIframe"
                style="width:100%;border:1px solid #e2e8f0;border-radius:8px;min-height:400px;"
                sandbox="allow-same-origin"
                title="Aperçu email"></iframe>
        </div>
        <div style="padding:16px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;text-align:right;">
            <button onclick="closeRetentionPreview()" style="margin-right:8px;padding:8px 16px;border:1px solid #e2e8f0;border-radius:6px;cursor:pointer;background:#fff;">Fermer</button>
            <button onclick="closeRetentionPreview(); submitRetentionCampaign()" style="padding:8px 16px;background:#0ea5e9;color:#fff;border:none;border-radius:6px;cursor:pointer;">📤 Envoyer maintenant</button>
        </div>
    </div>
</div>

@script
<script>
window.previewRetention = function() {
    var msg = $wire.get('data.message') || '';
    if (!msg || msg === '<p><br></p>') {
        alert('Rédigez un message avant de prévisualiser.');
        return;
    }
    var full = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>' +
        'body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;font-size:14px;line-height:1.7;color:#1e293b;max-width:600px;margin:0 auto;padding:24px}' +
        'h1,h2,h3{color:#0f172a} a{color:#0ea5e9}' +
        'blockquote{border-left:3px solid #e2e8f0;margin:0;padding-left:16px;color:#64748b}' +
        'pre,code{background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:13px}' +
        'ul,ol{padding-left:24px} img{max-width:100%}' +
        '</style></head><body>' + msg + '</body></html>';

    document.getElementById('retentionPreviewIframe').srcdoc = full;
    document.getElementById('retentionPreviewModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
};
window.closeRetentionPreview = function() {
    document.getElementById('retentionPreviewModal').style.display = 'none';
    document.body.style.overflow = '';
};
window.submitRetentionCampaign = function() {
    $wire.call('sendCampaign');
};
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('retentionPreviewModal').style.display === 'block') {
        closeRetentionPreview();
    }
});
document.getElementById('retentionPreviewModal').addEventListener('click', function(e) {
    if (e.target === this) closeRetentionPreview();
});
</script>
@endscript
</x-filament-panels::page>
