<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<div id="legalTinyMCE"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        ['privacy_policy', 'terms_conditions', 'cookie_policy'].forEach(function(field) {
            const textarea = document.querySelector('[wire\\:model*="' + field + '"]');
            if (textarea) {
                const id = 'tinymce_' + field;
                textarea.id = id;
                textarea.style.display = 'none';

                const container = document.createElement('div');
                container.id = 'container_' + field;
                textarea.parentNode.insertBefore(container, textarea);

                tinymce.init({
                    selector: '#container_' + field,
                    height: 300,
                    menubar: true,
                    plugins: 'lists link image code preview fullscreen table',
                    toolbar: 'undo redo | blocks | bold italic underline strikethrough | forecolor backcolor | link image table | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | code preview fullscreen',
                    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; font-size: 14px; }',
                    setup: function(editor) {
                        editor.on('change', function() {
                            textarea.value = editor.getContent();
                            textarea.dispatchEvent(new Event('input', { bubbles: true }));
                        });
                    },
                    init_instance_callback: function(editor) {
                        if (textarea.value) {
                            editor.setContent(textarea.value);
                        }
                    }
                });
            }
        });
    }, 500);
});
</script>
