<x-filament-panels::page>
    <form wire:submit="sendCampaign">
        {{ $this->form }}

        <div style="margin-top:16px;">
            <style>
            .html-editor-wrap { border:1px solid #d1d5db; border-radius:8px; overflow:hidden; background:#fff; margin-bottom:16px; }
            .html-editor-tabs { display:flex; background:#f1f5f9; border-bottom:1px solid #d1d5db; }
            .html-editor-tab { padding:8px 16px; font-size:13px; font-weight:600; cursor:pointer; border:none; background:none; color:#64748b; }
            .html-editor-tab.active { background:#fff; color:#0ea5e9; border-bottom:2px solid #0ea5e9; }
            .html-editor-source { width:100%; min-height:200px; font-family:monospace; font-size:13px; border:none; padding:12px; resize:vertical; background:#1e293b; color:#e2e8f0; }
            .html-editor-preview { width:100%; min-height:200px; border:none; display:none; background:#fff; }
            .html-editor-preview.active { display:block; }
            .html-editor-wrap iframe { width:100%; min-height:300px; border:none; }
            </style>

            <div class="html-editor-wrap" data-field="message">
                <div class="html-editor-tabs">
                    <button type="button" class="html-editor-tab active" onclick="htmlEditorSwitchTab(this, 'source')">📝 Code source</button>
                    <button type="button" class="html-editor-tab" onclick="htmlEditorSwitchTab(this, 'preview')">👁 Aperçu</button>
                </div>
                <textarea class="html-editor-source" id="adminHtmlSource" placeholder="Collez votre code HTML ici..."></textarea>
                <div class="html-editor-preview">
                    <iframe id="adminHtmlPreview"></iframe>
                </div>
            </div>
        </div>

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
                type="submit"
                color="success"
                icon="heroicon-o-paper-airplane"
                wire:confirm="{{ __('app.client.retention.send_confirm') }}"
            >
                {{ __('app.client.retention.send') }}
            </x-filament::button>
        </div>
    </form>

    <div class="mt-8 rounded-xl border border-gray-200 bg-gray-50 dark:bg-gray-900 dark:border-gray-700 p-5 text-sm text-gray-500">
        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ __('app.admin.retention_strategies_title') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-blue-600 dark:text-blue-400 mb-2">{{ __('app.admin.ret_strat_renewal') }}</h4>
                <p class="text-xs">{{ __('app.admin.ret_strat_renewal_desc') }}</p>
            </div>
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-green-600 dark:text-green-400 mb-2">{{ __('app.admin.ret_strat_upgrade') }}</h4>
                <p class="text-xs">{{ __('app.admin.ret_strat_upgrade_desc') }}</p>
            </div>
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-amber-600 dark:text-amber-400 mb-2">{{ __('app.admin.ret_strat_winback') }}</h4>
                <p class="text-xs">{{ __('app.admin.ret_strat_winback_desc') }}</p>
            </div>
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-purple-600 dark:text-purple-400 mb-2">{{ __('app.admin.ret_strat_feedback') }}</h4>
                <p class="text-xs">{{ __('app.admin.ret_strat_feedback_desc') }}</p>
            </div>
        </div>
    </div>

    <script>
    function htmlEditorSwitchTab(el, tab) {
        const wrap = el.closest('.html-editor-wrap');
        wrap.querySelectorAll('.html-editor-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        const source = wrap.querySelector('.html-editor-source');
        const previewWrap = wrap.querySelector('.html-editor-preview');
        if (tab === 'preview') {
            wrap.querySelector('iframe').srcdoc = source.value;
            previewWrap.classList.add('active');
            source.style.display = 'none';
        } else {
            previewWrap.classList.remove('active');
            source.style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const source = document.getElementById('adminHtmlSource');
            if (!source) return;
            const targetField = document.querySelector('[wire\\:model*="message"]');
            if (targetField) {
                source.value = targetField.value || '';
                source.addEventListener('input', function() {
                    targetField.value = source.value;
                    targetField.dispatchEvent(new Event('input', { bubbles: true }));
                });
                const observer = new MutationObserver(function() {
                    if (targetField.value !== source.value) {
                        source.value = targetField.value;
                    }
                });
                observer.observe(targetField, { attributes: true, attributeFilter: ['value'] });
            }
        }, 500);
    });
    </script>
</x-filament-panels::page>
