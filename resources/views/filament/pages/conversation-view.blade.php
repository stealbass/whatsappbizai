<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLONNE GAUCHE : infos contact + résumé --}}
        <div class="space-y-4">
            {{-- Carte contact --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:bg-gray-900 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Contact</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-content-center text-sky-600 font-bold text-lg flex items-center justify-center">
                        {{ strtoupper(substr($this->record->contact->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-white">{{ $this->record->contact->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-gray-500">{{ $this->record->contact->whatsapp_number }}</p>
                    </div>
                </div>
                @if($this->record->contact->company)
                <p class="mt-2 text-sm text-gray-500">🏢 {{ $this->record->contact->company }}</p>
                @endif
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                        {{ $this->record->contact->status === 'client' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($this->record->contact->status ?? 'prospect') }}
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                        {{ $this->record->ai_enabled ? 'bg-sky-100 text-sky-700' : 'bg-red-100 text-red-700' }}">
                        {{ $this->record->ai_enabled ? '🤖 AI active' : '⏸ AI paused' }}
                    </span>
                </div>

                {{-- Toggle AI --}}
                <form method="POST" action="#" class="mt-3">
                    @csrf
                    <button type="button"
                        wire:click="toggleAI"
                        class="text-xs px-3 py-1.5 rounded-lg border font-medium transition
                            {{ $this->record->ai_enabled
                                ? 'border-red-300 text-red-600 hover:bg-red-50'
                                : 'border-sky-300 text-sky-600 hover:bg-sky-50' }}">
                        {{ $this->record->ai_enabled ? '⏸ Pause AI' : '▶ Enable AI' }}
                    </button>
                </form>
            </div>

            {{-- AI Summary --}}
            @if($this->record->summary)
            <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 dark:bg-sky-950 dark:border-sky-800">
                <h3 class="text-sm font-semibold text-sky-700 dark:text-sky-300 mb-2">📝 AI Summary</h3>
                <p class="text-sm text-sky-900 dark:text-sky-200 leading-relaxed">{{ $this->record->summary }}</p>
            </div>
            @endif

            {{-- Stats --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:bg-gray-900 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Stats</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total messages</span>
                        <span class="font-semibold">{{ $this->record->messages->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">AI replies</span>
                        <span class="font-semibold text-sky-600">{{ $this->record->messages->where('is_ai', true)->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Opened</span>
                        <span class="font-semibold">{{ $this->record->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Last message</span>
                        <span class="font-semibold">{{ $this->record->last_message_at?->diffForHumans() ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:bg-gray-900 dark:border-gray-700 space-y-2">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Quick Actions</h3>
                <button wire:click="summarize"
                    class="w-full text-left text-sm px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition">
                    📝 Generate AI summary
                </button>
                <button wire:click="suggestReply"
                    class="w-full text-left text-sm px-3 py-2 rounded-lg border border-sky-200 text-sky-700 hover:bg-sky-50 transition">
                    💡 Suggest a reply
                </button>
            </div>
        </div>

        {{-- COLONNE DROITE : fil de messages + saisie --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Suggested reply banner --}}
            @if($this->suggestedReply)
            <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 dark:bg-sky-950 dark:border-sky-800">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold text-sky-600 mb-1">💡 AI suggested reply</p>
                        <p class="text-sm text-sky-900 dark:text-sky-200 whitespace-pre-wrap">{{ $this->suggestedReply }}</p>
                    </div>
                    <button wire:click="useSuggestion"
                        class="shrink-0 text-xs px-3 py-1.5 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition">
                        Use
                    </button>
                </div>
            </div>
            @endif

            {{-- Messages --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden dark:bg-gray-900 dark:border-gray-700">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200">WhatsApp Conversation</h3>
                    <span class="text-xs text-gray-400">{{ $this->record->messages->count() }} message(s)</span>
                </div>

                <div class="p-4 space-y-3 max-h-[500px] overflow-y-auto bg-gray-50 dark:bg-gray-950" id="msg-list">
                    @forelse($this->record->messages()->orderBy('created_at')->get() as $message)
                    <div class="flex {{ $message->direction === 'outbound' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[78%]">
                            <div class="rounded-2xl px-4 py-2.5 text-sm leading-relaxed
                                {{ $message->direction === 'outbound'
                                    ? ($message->is_ai ? 'bg-sky-600 text-white rounded-br-sm' : 'bg-gray-700 text-white rounded-br-sm')
                                    : 'bg-white border border-gray-200 text-gray-800 rounded-bl-sm shadow-sm dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600' }}">
                                @if($message->is_ai)
                                <div class="text-xs opacity-70 mb-1">🤖 AI Agent</div>
                                @endif
                                {!! nl2br(e($message->content)) !!}
                            </div>
                            <div class="text-xs text-gray-400 mt-1 {{ $message->direction === 'outbound' ? 'text-right' : 'text-left' }}">
                                {{ $message->created_at->format('d/m H:i') }}
                                @if($message->direction === 'outbound')
                                · {{ match($message->status) { 'read' => '✓✓', 'delivered' => '✓✓', 'sent' => '✓', default => '⏳' } }}
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-12 text-sm">No messages yet.</div>
                    @endforelse
                </div>

                {{-- Manual reply input --}}
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-900">
                    <div class="flex gap-2 items-end">
                        <textarea
                            wire:model="replyText"
                            rows="2"
                            placeholder="Type a manual reply..."
                            class="flex-1 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm px-3 py-2 resize-none focus:outline-none focus:ring-2 focus:ring-sky-400 dark:text-white"
                        ></textarea>
                        <button wire:click="sendManualReply"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-xl transition disabled:opacity-50">
                            <span wire:loading.remove>Send ✈</span>
                            <span wire:loading>…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll messages to bottom
        document.addEventListener('livewire:navigated', () => {
            const el = document.getElementById('msg-list');
            if (el) el.scrollTop = el.scrollHeight;
        });
        const el = document.getElementById('msg-list');
        if (el) el.scrollTop = el.scrollHeight;
    </script>
</x-filament-panels::page>
