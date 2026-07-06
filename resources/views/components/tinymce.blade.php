{{--
    Composant TinyMCE auto-hébergé — aucune API key requise
    Usage : @include('components.tinymce')  puis  initTinyMCE('#id', 350)
--}}
@once
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
@endonce

<style>
    /* Masque la notification "no-api-key" si le CDN est utilisé en fallback */
    .tox-notification { display: none !important; }
    .tox-statusbar__branding { display: none !important; }
    .tox-tinymce { border-radius: 8px !important; border: 1px solid #e2e8f0 !important; }
</style>

<script>
/* Prevent TinyMCE from hijacking Livewire/Alpine focusin */
document.addEventListener('focusin', function(e) {
    if (e.target.closest && e.target.closest(
        '.tox-tinymce-aux, .tox-tinymce, .tox-editor-header, .tox-dialog, .tox-pop'
    )) { e.stopImmediatePropagation(); }
}, true);

/**
 * initTinyMCE(selector, height)
 * Initialise un éditeur WYSIWYG riche sur un <textarea>.
 * Supporte le mode HTML source (bouton "Code").
 *
 * @param {string}  selector  — sélecteur CSS du textarea (ex: '#message')
 * @param {number}  height    — hauteur en px (défaut 350)
 */
function initTinyMCE(selector, height) {
    height = height || 350;

    // Retry si TinyMCE n'est pas encore chargé
    if (typeof tinymce === 'undefined') {
        setTimeout(function() { initTinyMCE(selector, height); }, 250);
        return;
    }

    // Nettoie une éventuelle instance précédente
    var existing = tinymce.get(selector.replace('#', ''));
    if (existing) existing.destroy();

    tinymce.init({
        selector:  selector,
        height:    height,
        promotion: false,
        branding:  false,
        license_key: 'gpl',            // TinyMCE 6 self-hosted = GPL
        menubar:   'file edit view insert format tools table',
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
        /* Sync le contenu dans le textarea original à chaque modification */
        setup: function(editor) {
            editor.on('change keyup undo redo', function() {
                editor.save();
                var ta = document.querySelector(selector);
                if (ta) ta.dispatchEvent(new Event('input', { bubbles: true }));
            });
        }
    });
}

/*
 * Global form submit hook — saves ALL TinyMCE instances before any form submits.
 * Prevents empty textarea values when the user clicks submit without triggering
 * a 'change' event (e.g. paste then immediately submit).
 */
document.addEventListener('submit', function(e) {
    if (typeof tinymce !== 'undefined') {
        tinymce.triggerSave();
    }
}, true); // capture phase so it runs before any form submit handler

</script>
