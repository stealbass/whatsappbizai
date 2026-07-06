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

function initTinyMCE(selector, height) {
    tinymce.init({
        selector: selector,
        height: height || 350,
        promotion: false,
        menubar: true,
        plugins: 'lists link image code preview fullscreen table',
        toolbar: 'undo redo | blocks | bold italic underline strikethrough | forecolor backcolor | link image table | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | code preview fullscreen',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; font-size: 14px; }',
        setup: function(editor) {
            editor.on('change', function() { editor.save(); });
        }
    });
}
</script>
