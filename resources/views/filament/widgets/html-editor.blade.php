<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<style>
    .tox-notification { display: none !important; }
</style>
<script>
document.addEventListener('focusin', function(e) {
    if (e.target.closest && e.target.closest('.tox-tinymce-aux, .tox-tinymce, .tox-editor-header, .tox-dialog, .tox-pop')) {
        e.stopImmediatePropagation();
    }
}, true);

function initSiteSettingEditor(field) {
    var ta = document.querySelector('[wire\\:model*="' + field + '"]');
    if (!ta) return;
    var editorId = 'tinymce_' + field;
    ta.id = editorId;
    ta.style.display = 'none';
    var container = document.createElement('div');
    container.id = 'container_' + field;
    ta.parentNode.insertBefore(container, ta);
    var ed = tinymce.get(editorId);
    if (ed) ed.destroy();
    tinymce.init({
        selector: '#container_' + field,
        height: 300, menubar: true, promotion: false,
        plugins: 'lists link image code preview fullscreen table',
        toolbar: 'undo redo | blocks | bold italic underline strikethrough | forecolor backcolor | link image table | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | code preview fullscreen',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; font-size: 14px; }',
        setup: function(editor) {
            editor.on('change', function() {
                ta.value = editor.getContent();
                ta.dispatchEvent(new Event('input', { bubbles: true }));
            });
        },
        init_instance_callback: function(editor) {
            if (ta.value) editor.setContent(ta.value);
        }
    });
}

function bootSiteSettingEditors() {
    if (typeof tinymce === 'undefined') { setTimeout(bootSiteSettingEditors, 300); return; }
    ['privacy_policy', 'terms_conditions', 'cookie_policy', 'footer_description'].forEach(initSiteSettingEditor);
}

document.addEventListener('DOMContentLoaded', bootSiteSettingEditors);
document.addEventListener('livewire:navigated', bootSiteSettingEditors);
</script>
