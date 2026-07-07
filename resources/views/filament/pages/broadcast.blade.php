<x-filament-panels::page>

    <form wire:submit="send" id="broadcast-form">
        {{ $this->form }}

        <div class="mt-6 flex flex-wrap justify-end gap-3">
            <x-filament::button
                type="button"
                color="info"
                icon="heroicon-o-sparkles"
                wire:click="draftWithAI"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="draftWithAI">🤖 Draft with AI</span>
                <span wire:loading wire:target="draftWithAI">Generating…</span>
            </x-filament::button>

            <x-filament::button
                type="button"
                color="gray"
                icon="heroicon-o-eye"
                wire:click="previewContent"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="previewContent">👁️ Aperçu</span>
                <span wire:loading wire:target="previewContent">⏳</span>
            </x-filament::button>

            <x-filament::button
                type="submit"
                color="success"
                icon="heroicon-o-paper-airplane"
                wire:confirm="Ce message sera envoyé à tous les destinataires sélectionnés. Confirmer ?"
            >
                📤 Send broadcast
            </x-filament::button>
        </div>
    </form>

    <div class="mt-8 rounded-xl border border-gray-200 bg-gray-50 dark:bg-gray-900 dark:border-gray-700 p-5 text-sm text-gray-500">
        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">💡 How to use AI drafting</h3>
        <ol class="space-y-1 list-decimal list-inside">
            <li>Select your target audience (all, clients, or prospects)</li>
            <li>Type your goal in the <strong>AI goal</strong> field</li>
            <li>Click <strong>Draft with AI</strong> — Gemini writes the message using your catalog</li>
            <li>Click <strong>👁️ Aperçu</strong> to preview the rendered HTML before sending</li>
            <li>Click <strong>Send broadcast</strong> to dispatch to all recipients</li>
        </ol>
        <p class="mt-3">Variables: <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{nom}}' !!}</code>, <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{prenom}}' !!}</code>, <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{entreprise}}' !!}</code></p>
        <p class="mt-2 text-amber-600 dark:text-amber-400">⚠️ WhatsApp requires approved templates for bulk outreach. Use within the 24-hour conversation window.</p>
    </div>

    @if($showPreview)
    {{-- Store raw HTML as base64 so Blade encoding doesn't corrupt it --}}
    <script type="text/html" id="bcast-data">{!! base64_encode($previewHtml) !!}</script>

    <div
        class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto"
        style="background:rgba(0,0,0,.55); padding: 40px 16px;"
        wire:click.self="$set('showPreview', false)"
    >
        <div style="width:100%;max-width:720px;background:#fff;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <span style="font-weight:700;font-size:15px;">👁️ Aperçu du message</span>
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="font-size:11px;color:#64748b;background:#e2e8f0;padding:3px 10px;border-radius:99px;">Rendu réel reçu par le destinataire</span>
                    <button type="button" wire:click="$set('showPreview', false)" style="background:none;border:none;cursor:pointer;font-size:22px;color:#94a3b8;line-height:1;">&times;</button>
                </div>
            </div>
            <div style="padding:12px 20px;background:#e9ecef;border-bottom:1px solid #dee2e6;font-size:12px;color:#6c757d;font-family:monospace;">
                <div><strong>De :</strong> {{ config('mail.from.name', 'WhatsAppBizAI') }} &lt;{{ config('mail.from.address', 'noreply@example.com') }}&gt;</div>
                <div><strong>Segment :</strong> {{ $data['target'] ?? 'all' }}</div>
            </div>
            <div style="padding:20px;">
                <iframe id="broadcast-preview-iframe" style="width:100%;min-height:400px;border:1px solid #e2e8f0;border-radius:8px;" title="Aperçu message"></iframe>
            </div>
            <div style="padding:16px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                <span style="font-size:12px;color:#64748b;"><code>&lt;!DOCTYPE html&gt;</code> détecté automatiquement → rendu tel quel.</span>
                <div style="display:flex;gap:8px;">
                    <x-filament::button type="button" color="gray" wire:click="$set('showPreview', false)">Fermer</x-filament::button>
                    <x-filament::button type="button" color="success" icon="heroicon-o-paper-airplane" wire:click="send" wire:confirm="Ce message sera envoyé à tous les destinataires sélectionnés. Confirmer ?">📤 Envoyer maintenant</x-filament::button>
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
            var el = document.getElementById('bcast-data');
            var iframe = document.getElementById('broadcast-preview-iframe');
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
