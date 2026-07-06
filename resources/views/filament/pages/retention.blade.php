<x-filament-panels::page>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <form wire:submit="sendCampaign">
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

    @script
    <script>
        tinymce.init({
            selector: '[wire\\:model="message"]',
            height: 350,
            menubar: true,
            plugins: 'lists link image code preview fullscreen table',
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | forecolor backcolor | link image table | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | code preview fullscreen',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; font-size: 14px; }',
            setup: function(editor) {
                editor.on('change', function() {
                    $wire.set('message', editor.getContent());
                });
            },
            init_instance_callback: function(editor) {
                editor.setContent($wire.get('message') || '');
            }
        });
    </script>
    @endscript
</x-filament-panels::page>
