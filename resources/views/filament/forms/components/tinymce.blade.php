{{--
    Filament TinyMCE field — Livewire-safe
    ─────────────────────────────────────────────────────────────────────────
    Architecture :
    • wire:ignore       → Livewire ne touche JAMAIS ce sous-arbre DOM après
                          le premier rendu
    • Alpine x-init     → boot TinyMCE une fois, re-boot sur navigation SPA
    • @this.set()       → sync la valeur vers Livewire à chaque changement
    • textarea visible  → NE PAS mettre display:none avant l'init TinyMCE
                          (casse le calcul de hauteur) ; TinyMCE le cache lui-même
    ─────────────────────────────────────────────────────────────────────────
--}}
@php
    $statePath          = $getStatePath();
    $editorId           = 'tinymce_' . str_replace(['.', '[', ']', '-', ':'], '_', $statePath);
    $height             = $getHeight();
    $initialVal         = $getState() ?? '';
    $isDisabled         = $isDisabled();
    $editorPlaceholder  = $getEditorPlaceholder();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    {{-- wire:ignore = Livewire never diffs this subtree after first render --}}
    <div
        wire:ignore
        x-data="{
            editorId:  '{{ $editorId }}',
            statePath: '{{ $statePath }}',
            height:     {{ $height }},
            isDisabled: {{ $isDisabled ? 'true' : 'false' }},
            placeholder: @js($editorPlaceholder),
            editor:     null,

            init() {
                this.bootEditor();
                /* Re-init after Livewire SPA navigation */
                document.addEventListener('livewire:navigated', () => this.bootEditor());
            },

            bootEditor() {
                const self = this;
                /* Wait until TinyMCE script is loaded */
                if (typeof tinymce === 'undefined') {
                    setTimeout(() => self.bootEditor(), 300);
                    return;
                }
                /* Destroy stale instance */
                const existing = tinymce.get(self.editorId);
                if (existing) existing.destroy();

                tinymce.init({
                    selector:    '#' + self.editorId,
                    height:      self.height,
                    promotion:   false,
                    branding:    false,
                    license_key: 'gpl',
                    readonly:    self.isDisabled,
                    placeholder: self.placeholder || undefined,
                    menubar:     'edit view insert format tools table',
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                        'preview', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'wordcount'
                    ],
                    toolbar:
                        'undo redo | blocks | ' +
                        'bold italic underline strikethrough | ' +
                        'forecolor backcolor | ' +
                        'alignleft aligncenter alignright alignjustify | ' +
                        'bullist numlist outdent indent | ' +
                        'link image table | removeformat | code fullscreen',
                    content_style:
                        'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", ' +
                        'sans-serif; font-size: 14px; line-height: 1.6; ' +
                        'color: #1e293b; padding: 8px 12px; }',

                    setup(editor) {
                        self.editor = editor;

                        /* Load initial value once the editor is ready */
                        editor.on('init', function() {
                            /* textarea already has $initialVal via server render */
                            const ta = document.getElementById(self.editorId);
                            if (ta && ta.value) {
                                editor.setContent(ta.value);
                            }
                        });

                        /* Sync every content change back to Livewire */
                        editor.on('change keyup undo redo', function() {
                            const html = editor.getContent();
                            @this.set(self.statePath, html);
                        });
                    }
                });
            }
        }"
        x-init="init()"
    >
        {{--
            DO NOT set display:none here — TinyMCE needs the textarea
            visible to calculate its own height during init.
            TinyMCE will hide it automatically once it takes over.
            IMPORTANT: use {!! !!} — not {{ }} — to avoid double-escaping HTML content.
        --}}
        <textarea
            id="{{ $editorId }}"
            style="width:100%; min-height:{{ $height }}px;"
        >{!! $initialVal !!}</textarea>
    </div>
</x-dynamic-component>
