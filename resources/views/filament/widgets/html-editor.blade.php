{{--
    html-editor.blade.php — Widget TinyMCE pour SiteSettingResource
    Utilise TinyMCE self-hosted (public/vendor/tinymce/) — aucune API key
    Le script TinyMCE est déjà injecté via AdminPanelProvider::renderHook()
--}}
<script>
function initSiteSettingEditor(field) {
    if (typeof tinymce === 'undefined') {
        setTimeout(function() { initSiteSettingEditor(field); }, 300);
        return;
    }

    // Find the Livewire textarea for this field (wire:model contains field name)
    var ta = document.querySelector('[wire\\:model*="' + field + '"], [wire\\:model\\.live*="' + field + '"], [wire\\:model\\.lazy*="' + field + '"]');
    if (!ta) return;

    var editorId = 'tinymce_sitesetting_' + field;
    ta.id = editorId;

    // Destroy previous instance
    var existing = tinymce.get(editorId);
    if (existing) existing.destroy();

    tinymce.init({
        selector:    '#' + editorId,
        height:      400,
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
            'bold italic underline strikethrough | forecolor backcolor | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | ' +
            'link image table | removeformat | code fullscreen',
        content_style:
            'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; ' +
            'font-size: 14px; line-height: 1.6; padding: 8px 12px; }',

        setup: function(editor) {
            editor.on('init', function() {
                if (ta.value) editor.setContent(ta.value);
            });
            editor.on('change keyup undo redo', function() {
                ta.value = editor.getContent();
                // Trigger Livewire update
                ta.dispatchEvent(new Event('input', { bubbles: true }));
                ta.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }
    });
}

function bootSiteSettingEditors() {
    ['privacy_policy', 'terms_conditions', 'cookie_policy', 'footer_description'].forEach(initSiteSettingEditor);
}

document.addEventListener('DOMContentLoaded', bootSiteSettingEditors);
document.addEventListener('livewire:navigated', bootSiteSettingEditors);
document.addEventListener('livewire:init', function() {
    Livewire.hook('morph.updated', function() {
        // Don't re-init on every Livewire update — TinyMCE handles its own DOM
    });
});
</script>
