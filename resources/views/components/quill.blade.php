{{--
    Composant Quill.js — WYSIWYG via CDN jsDelivr (aucune API key, aucun asset local requis)
    Usage :
        @include('components.quill')
        puis : initQuill('#textarea-id', 300)
--}}
@once
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<style>
    .ql-container { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; font-size: 14px; }
    .ql-toolbar.ql-snow { border-radius: 8px 8px 0 0; border-color: #cbd5e1; background: #f8fafc; }
    .ql-container.ql-snow { border-radius: 0 0 8px 8px; border-color: #cbd5e1; }
    .ql-editor { min-height: 120px; line-height: 1.6; color: #1e293b; }
    .ql-editor.ql-blank::before { color: #94a3b8; font-style: normal; }
</style>
@endonce

<script>
/**
 * initQuill(textareaSelector, height)
 * Monte un éditeur Quill.js riche sur un <textarea> existant.
 * Le textarea est caché et reste synchronisé avec l'éditeur.
 *
 * @param {string} textareaSelector  — sélecteur CSS du textarea (ex: '#notes')
 * @param {number} height            — hauteur de la zone éditable en px (défaut: 280)
 */
// Global registry: window._quillInstances['#id'] = quillInstance
window._quillInstances = window._quillInstances || {};

/**
 * setQuillContent(selector, html)
 * Injecte du contenu HTML dans un éditeur Quill déjà initialisé.
 */
function setQuillContent(selector, html) {
    var q = window._quillInstances[selector];
    if (q) { q.root.innerHTML = html || ''; }
}

function initQuill(textareaSelector, height) {
    height = height || 280;

    var ta = document.querySelector(textareaSelector);
    if (!ta) { console.warn('initQuill: element not found:', textareaSelector); return; }

    // Si déjà initialisé, on skip
    if (ta.dataset.quillReady) return;
    ta.dataset.quillReady = '1';

    // Masque le textarea natif
    ta.style.display = 'none';

    // Crée la div hôte de Quill juste avant le textarea
    var editorDiv = document.createElement('div');
    editorDiv.style.height = height + 'px';
    ta.parentNode.insertBefore(editorDiv, ta);

    var toolbarOptions = [
        [{ header: [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ color: [] }, { background: [] }],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ align: [] }],
        ['link', 'blockquote', 'code-block'],
        ['clean']
    ];

    var quill = new Quill(editorDiv, {
        theme: 'snow',
        placeholder: ta.placeholder || '',
        modules: { toolbar: toolbarOptions }
    });

    // Register instance for external access (e.g. setQuillContent)
    window._quillInstances[textareaSelector] = quill;

    // Charge la valeur initiale du textarea (HTML)
    if (ta.value && ta.value.trim()) {
        quill.root.innerHTML = ta.value;
    }

    // Sync textarea ← Quill à chaque modification
    function syncToTextarea() {
        var html = quill.root.innerHTML;
        // Quill retourne '<p><br></p>' pour vide — on normalise à ''
        ta.value = (html === '<p><br></p>') ? '' : html;
        ta.dispatchEvent(new Event('input', { bubbles: true }));
        ta.dispatchEvent(new Event('change', { bubbles: true }));
    }

    quill.on('text-change', syncToTextarea);

    // Sync forcée à la soumission du formulaire (sécurité)
    var form = ta.closest('form');
    if (form) {
        form.addEventListener('submit', function() { syncToTextarea(); }, true);
    }
}
</script>
