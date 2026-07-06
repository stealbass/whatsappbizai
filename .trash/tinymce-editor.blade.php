{{--
    Vue Blade pour le champ Filament TinyMceEditor
    Alpine.js + wire:ignore empêchent Livewire de détruire l'éditeur lors des re-renders.
    La valeur est synchronisée via $wire.entangle() en mode defer.
--}}
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $statePath  = $getStatePath();
        $editorId   = 'tinymce_' . str_replace(['.', '[', ']', '-'], '_', $statePath);
        $height     = $field->getHeight();
        $isDisabled = $isDisabled();
    @endphp

    <div
        wire:ignore
        x-data="{
            editorId: '{{ $editorId }}',
            value: $wire.entangle('{{ $statePath }}').defer,
            editor: null,

            init() {
                this.bootEditor();
                /* Re-initialise si Livewire navigue (SPA) */
                document.addEventListener('livewire:navigated', () => this.bootEditor());
            },

            bootEditor() {
                const self = this;
                /* Attend que tinymce soit chargé */
                if (typeof tinymce === 'undefined') {
                    setTimeout(() => self.bootEditor(), 300);
                    return;
                }
                /* Détruit l'instance précédente si elle existe */
                if (tinymce.get(self.editorId)) tinymce.get(self.editorId).destroy();

                tinymce.init({
                    selector:    '#' + self.editorId,
                    height:      {{ $height }},
                    promotion:   false,
                    branding:    false,
                    license_key: 'gpl',
                    menubar:     'file edit view insert format tools table',
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                        'preview', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'wordcount'
                    ],
                    toolbar:
                        'undo redo | blocks | ' +
                        'bold italic underline strikethrough | forecolor backcolor | ' +
                        'alignleft aligncenter alignright alignjustify | ' +
                        'bullist numlist outdent indent | ' +
                        'link image table | removeformat | code fullscreen',
                    content_style:
                        'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; ' +
                        'font-size: 14px; line-height: 1.6; color: #1e293b; padding: 8px 12px; }',
                    readonly: {{ $isDisabled ? 'true' : 'false' }},

                    setup(editor) {
                        self.editor = editor;

                        /* Charge la valeur initiale dès que l'éditeur est prêt */
                        editor.on('init', function() {
                            if (self.value) editor.setContent(self.value);
                        });

                        /* Sync vers Livewire à chaque modification */
                        editor.on('change keyup undo redo', function() {
                            self.value = editor.getContent();
                        });
                    }
                });
            },

            /* Quand Livewire met à jour la valeur en dehors de l'éditeur (ex: Reset) */
            syncFromWire(newVal) {
                if (this.editor && this.editor.getContent() !== newVal) {
                    this.editor.setContent(newVal || '');
                }
            }
        }"
        x-init="init()"
        x-effect="syncFromWire(value)"
    >
        <textarea
            id="{{ $editorId }}"
            style="visibility:hidden; width:100%; height:{{ $height }}px;"
        ></textarea>
    </div>
</x-dynamic-component>
