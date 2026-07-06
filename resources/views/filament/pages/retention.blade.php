<x-filament-panels::page>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/lang/summernote-fr-FR.min.js"></script>

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

@script
<script>
var el = document.querySelector('[wire\\:model="message"]');
if (el && typeof jQuery !== 'undefined' && !el._snInit) {
    el._snInit = true;

    // Initialiser Summernote
    jQuery(el).summernote({
        height: 350,
        lang: 'fr-FR',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['codeview', 'fullscreen']],
        ],
        callbacks: {
            onChange: function(html) {
                el.value = html;
            },
            onInit: function() {
                var val = $wire.get('message') || '';
                if (val) { jQuery(this).summernote('code', val); }
            }
        }
    });

    // Submit : sync le contenu avant l'envoi
    document.getElementById('retention-form').addEventListener('submit', function() {
        $wire.set('message', jQuery(el).summernote('code'), false);
    });


}
</script>
@endscript
