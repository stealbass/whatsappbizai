<x-filament-panels::page>
    @include('components.quill')

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
                id="previewRetentionBtn"
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
            <div style="display:flex;gap:8px;align-items:center;">
                <span style="font-size:11px;color:#64748b;background:#e2e8f0;padding:3px 8px;border-radius:99px;">Rendu email réel</span>
                <button onclick="closeRetentionPreview()" style="background:none;border:none;cursor:pointer;font-size:20px;color:#94a3b8;line-height:1;">✕</button>
            </div>
        </div>
        <div style="padding:12px 20px;background:#e9ecef;border-bottom:1px solid #dee2e6;font-size:12px;color:#6c757d;font-family:monospace;">
            <div><strong>De :</strong> WhatsAppBizAI &lt;{{ config('mail.from.address', 'noreply@whatsappbizai.com') }}&gt;</div>
            <div><strong>Objet :</strong> {{ __('app.admin.retention_subject') }}</div>
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
var ta = document.querySelector('[wire\\:model="data.message"]');

// Already initialized (after re-render) → sync Quill content from Livewire
if (ta && ta.dataset.quillReady) {
    var val = $wire.get('data.message') || '';
    if (val) setQuillContent('[wire\\:model="data.message"]', val);
}

// First init
if (ta && !ta.dataset.quillReady) {
    initQuill('[wire\\:model="data.message"]', 400);
}

// Preview button
document.getElementById('previewRetentionBtn').addEventListener('click', function() {
    var q = window._quillInstances['[wire\\:model="data.message"]'];
    var html = q ? q.root.innerHTML : (ta ? ta.value : '');
    if (!html || html === '<p><br></p>') {
        alert('Rédigez un message avant de prévisualiser.');
        return;
    }
    var iframe = document.getElementById('retentionPreviewIframe');
    var doc = iframe.contentDocument || iframe.contentWindow.document;
    var full = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">' +
        '<style>' +
        '*{margin:0;padding:0;box-sizing:border-box}' +
        'body{font-family:Georgia,serif;font-size:16px;line-height:1.8;color:#333;max-width:600px;margin:0 auto;padding:32px 24px;background:#fff}' +
        '.email-body p{margin:0 0 14px;color:#333}' +
        '.email-body a{color:#0ea5e9;text-decoration:underline}' +
        '.email-body table{border-collapse:collapse;width:100%;margin:14px 0}' +
        '.email-body td,.email-body th{border:1px solid #e2e8f0;padding:8px 12px;text-align:left}' +
        '.email-body th{background:#f8fafc;font-weight:600}' +
        '.email-body img{max-width:100%;height:auto}' +
        '.email-body code,.email-body pre{background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:14px;font-family:monospace}' +
        '.email-body pre{padding:16px;overflow-x:auto;border:1px solid #e2e8f0}' +
        '.email-footer{margin-top:24px;padding:20px 0 0;border-top:2px solid #f0f0f0;font-size:13px;color:#94a3b8;text-align:center}' +
        '</style></head><body>' +
        '<div class="email-body">' + html + '</div>' +
        '<div class="email-footer"><p>WhatsAppBizAI</p></div>' +
        '</body></html>';
    doc.open();
    doc.write(full);
    doc.close();
    document.getElementById('retentionPreviewModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
});

window.closeRetentionPreview = function() {
    document.getElementById('retentionPreviewModal').style.display = 'none';
    document.body.style.overflow = '';
};
window.submitRetentionCampaign = function() {
    $wire.call('sendCampaign');
};
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('retentionPreviewModal').style.display === 'block')
        closeRetentionPreview();
});
document.getElementById('retentionPreviewModal').addEventListener('click', function(e) {
    if (e.target === this) closeRetentionPreview();
});
</script>
@endscript
</x-filament-panels::page>
