<x-filament-panels::page>
    <form wire:submit="send">
        {{ $this->form }}

        <div class="mt-6 flex flex-wrap justify-end gap-3">
            {{-- AI Draft button --}}
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

            {{-- Send button --}}
            <x-filament::button
                type="submit"
                color="success"
                icon="heroicon-o-paper-airplane"
                wire:confirm="This message will be sent to all selected recipients. Confirm?"
            >
                📤 Send broadcast
            </x-filament::button>
        </div>
    </form>

    {{-- Usage tips --}}
    <div class="mt-8 rounded-xl border border-gray-200 bg-gray-50 dark:bg-gray-900 dark:border-gray-700 p-5 text-sm text-gray-500">
        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">💡 How to use AI drafting</h3>
        <ol class="space-y-1 list-decimal list-inside">
            <li>Select your target audience (all, clients, or prospects)</li>
            <li>Type your goal in the <strong>AI goal</strong> field (e.g. "Announce a 20% promotion on web design")</li>
            <li>Click <strong>Draft with AI</strong> — Gemini will write the message using your service catalog</li>
            <li>Review and edit the generated message</li>
            <li>Click <strong>Send broadcast</strong> to dispatch to all recipients</li>
        </ol>
        <p class="mt-3">Available variables: <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{nom}}' !!}</code>, <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{prenom}}' !!}</code>, <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{entreprise}}' !!}</code></p>
        <p class="mt-2 text-amber-600 dark:text-amber-400">⚠️ WhatsApp requires approved message templates for bulk outreach. Use this feature within the 24-hour conversation window.</p>
    </div>
</x-filament-panels::page>
