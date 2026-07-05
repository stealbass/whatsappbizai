<x-filament-panels::page>
    <form wire:submit="send">
        {{ $this->form }}

        <div class="mt-6 flex flex-wrap justify-end gap-3">
            <x-filament::button
                type="button"
                color="info"
                icon="heroicon-o-sparkles"
                wire:click="draftWithAI"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="draftWithAI">🤖 Rédiger avec l'IA</span>
                <span wire:loading wire:target="draftWithAI">Génération…</span>
            </x-filament::button>

            <x-filament::button
                type="submit"
                color="success"
                icon="heroicon-o-paper-airplane"
                wire:confirm="Ce message sera envoyé à tous les destinataires sélectionnés. Confirmer ?"
            >
                📤 Envoyer la campagne
            </x-filament::button>
        </div>
    </form>

    <div class="mt-8 rounded-xl border border-gray-200 bg-gray-50 dark:bg-gray-900 dark:border-gray-700 p-5 text-sm text-gray-500">
        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">💡 Stratégies de rétention & acquisition</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-blue-600 dark:text-blue-400 mb-2">🔒 Rétention</h4>
                <ul class="space-y-1 text-xs">
                    <li>• Offre de bienvenue pour les nouveaux clients</li>
                    <li>• Réduction de fidélité après 3 achats</li>
                    <li>• Notification de renouvellement d'abonnement</li>
                </ul>
            </div>
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-green-600 dark:text-green-400 mb-2">📈 Upsell</h4>
                <ul class="space-y-1 text-xs">
                    <li>• Proposer un upgrade de plan</li>
                    <li>• Service additionnel pertinent</li>
                    <li>• Offre groupée à prix réduit</li>
                </ul>
            </div>
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-amber-600 dark:text-amber-400 mb-2">🔄 Win-back</h4>
                <ul class="space-y-1 text-xs">
                    <li>• Message après 30 jours d'inactivité</li>
                    <li>• Offre spéciale "On vous manque"</li>
                    <li>• Enquête de satisfaction</li>
                </ul>
            </div>
            <div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-purple-600 dark:text-purple-400 mb-2">👥 Parrainage</h4>
                <ul class="space-y-1 text-xs">
                    <li>• Programme de parrainage avec récompense</li>
                    <li>• Remise pour le parrain et le filleul</li>
                    <li>• Campagne "Parrainez un ami"</li>
                </ul>
            </div>
        </div>
        <p class="mt-3">Variables : <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{nom}}' !!}</code>, <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{prenom}}' !!}</code>, <code class="bg-gray-200 dark:bg-gray-800 px-1 rounded">{!! '{{entreprise}}' !!}</code></p>
        <p class="mt-2 text-amber-600 dark:text-amber-400">⚠️ WhatsApp nécessite des templates approuvés pour l'envoi en masse. Utilisez cette fonctionnalité dans la fenêtre de conversation de 24h.</p>
    </div>
</x-filament-panels::page>
