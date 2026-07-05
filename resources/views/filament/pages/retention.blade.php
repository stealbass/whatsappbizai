<x-filament-panels::page>
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
        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ __('app.client.retention.strategies_title') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-blue-600 dark:text-blue-400 mb-2">{{ __('app.client.retention.strat_retention') }}</h4>
                <ul class="space-y-1 text-xs">
                    <li>• {{ __('app.client.retention.strat_retention_desc') }}</li>
                </ul>
            </div>
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-green-600 dark:text-green-400 mb-2">{{ __('app.client.retention.strat_upsell') }}</h4>
                <ul class="space-y-1 text-xs">
                    <li>• {{ __('app.client.retention.strat_upsell_desc') }}</li>
                </ul>
            </div>
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-amber-600 dark:text-amber-400 mb-2">{{ __('app.client.retention.strat_winback') }}</h4>
                <ul class="space-y-1 text-xs">
                    <li>• {{ __('app.client.retention.strat_winback_desc') }}</li>
                </ul>
            </div>
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-purple-600 dark:text-purple-400 mb-2">{{ __('app.client.retention.strat_referral') }}</h4>
                <ul class="space-y-1 text-xs">
                    <li>• {{ __('app.client.retention.strat_referral_desc') }}</li>
                </ul>
            </div>
        </div>
        <p class="mt-3">{{ __('app.client.retention.variables') }} <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{nom}}' !!}</code>, <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{prenom}}' !!}</code>, <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{entreprise}}' !!}</code></p>
        <p class="mt-2 text-amber-600 dark:text-amber-400">⚠️ WhatsApp nécessite des templates approuvés pour l'envoi en masse. Utilisez cette fonctionnalité dans la fenêtre de conversation de 24h.</p>
    </div>
</x-filament-panels::page>
