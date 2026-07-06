{{--
    Filament TinyMCE field — Livewire-safe
    - wire:ignore prevents Livewire from re-rendering the editor DOM
    - Alpine.js x-init boots TinyMCE once and syncs via $wire.set()
    - TinyMCE is self-hosted from public/vendor/tinymce/ (no API key)
--}}
@php
    $statePath  = $getStatePath();
    $editorId   = 'tinymce_' . str_replace(['.', '[', ']', '-'], '_', $statePath);
    $height     = $getHeight();
    $initialVal = $getState() ?? '';
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    {{-- wire:ignore = Livewire never touches this DOM subtree after first render --}}
    <div wire:ignore>
        <textarea
            id="{{ $editorId }}"
            style="display:none;"
        >{{ $initialVal }}</textarea>

        <div
            id="{{ $editorId }}_container"
            x-data="{
                editorId: '{{ $editorId }}',
                statePath: '{{ $statePath }}',
                height: {{ $height }},
                editor: null,

                init() {
                    this.boot();
                    // Re-init on Livewire navigation (SPA mode)
                    document.addEventListener('livewire:navigated', () => this.boot());
                },

                boot() {
                    const self = this;
                    if (typeof tinymce === 'undefined') {
                        setTimeout(() => self.boot(), 300);
                        return;
                    }
                    // Destroy previous instance if any
                    if (tinymce.get(self.editorId)) tinymce.get(self.editorId).destroy();

                    tinymce.init({
                        selector:    '#' + self.editorId,
                        height:      self.height,
                        promotion:   false,
                        branding:    false,
                        license_key: 'gpl',
                        menubar:     'edit view insert format tools table',
                        plugins: [
                            'advlist','autolink','lists','link','image','charmap',
                            'preview','searchreplace','visualblocks','code','fullscreen',
                            'insertdatetime','media','table','wordcount'
                        ],
                        toolbar:
                            'undo redo | blocks | ' +
                            'bold italic underline strikethrough | ' +
                            'forecolor backcolor | ' +
                            'alignleft aligncenter alignright alignjustify | ' +
                            'bullist numlist outdent indent | ' +
                            'link image table | removeformat | code fullscreen',
                        content_style:
                            'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; ' +
                            'font-size: 14px; line-height: 1.6; color: #1e293b; padding: 8px 12px; }',

                        // Sync to Livewire state on every change
                        setup(editor) {
                            self.editor = editor;

                            editor.on('init', function() {
                                // Set initial value from Livewire state
                                const ta = document.getElementById(self.editorId);
                                if (ta && ta.value) editor.setContent(ta.value);
                            });

                            editor.on('change keyup undo redo', function() {
                                const content = editor.getContent();
                                // Push to Livewire component
                                @this.set(self.statePath, content);
                            });
                        }
                    });
                }
            }"
        >
            {{-- TinyMCE mounts itself here via selector '#editorId' --}}
        </div>
    </div>
</x-dynamic-component>
