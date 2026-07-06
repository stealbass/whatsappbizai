<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
function initTinyMCE(selector, height) {
    tinymce.init({
        selector: selector,
        height: height || 350,
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
