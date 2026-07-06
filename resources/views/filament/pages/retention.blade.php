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
                type="submit"
                color="success"
                icon="heroicon-o-paper-airplane"
                wire:confirm="{{ __('app.client.retention.send_confirm') }}"
            >
                {{ __('app.client.retention.send') }}
            </x-filament::button>
        </div>
    </form>

</x-filament-panels::page>
