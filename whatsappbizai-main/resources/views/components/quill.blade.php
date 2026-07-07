{{--
    Composant Quill.js v2 — WYSIWYG + toggle HTML source
    CDN jsDelivr — aucune API key, aucun asset local requis

    Usage :
        @include('components.quill')
        puis : initQuill('#textarea-id', 300)

    API publique :
        initQuill(selector, height)   — initialise l'éditeur
        setQuillContent(selector, html) — injecte du HTML dans un éditeur existant
--}}
@once
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<style>
    /* Quill editor styles */
    .ql-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        font-size: 14px;
    }
    .ql-toolbar.ql-snow {
        border-radius: 8px 8px 0 0;
        border-color: #cbd5e1;
        background: #f8fafc;
        flex-wrap: wrap;
    }
    .ql-container.ql-snow {
        border-radius: 0 0 8px 8px;
        border-color: #cbd5e1;
    }
    .ql-editor {
        min-height: 120px;
        line-height: 1.6;
        color: #1e293b;
    }
    .ql-editor.ql-blank::before {
        color: #94a3b8;
        font-style: normal;
    }

    /* HTML source toggle button */
    .ql-html-source::after {
        content: '</>';
        font-size: 11px;
        font-weight: 700;
        font-family: monospace;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
    }
    .ql-html-source.ql-active::after { color: #0ea5e9; }

    /* HTML source textarea (shown when toggle is active) */
    .ql-html-textarea {
        display: none;
        width: 100%;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.5;
        color: #1e293b;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-top: none;
        border-radius: 0 0 8px 8px;
        padding: 12px;
        resize: vertical;
        box-sizing: border-box;
        outline: none;
    }
    .ql-html-textarea:focus { border-color: #0ea5e9; }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .ql-toolbar.ql-snow { background: #1e293b; border-color: #334155; }
        .ql-container.ql-snow { border-color: #334155; }
        .ql-editor { color: #e2e8f0; }
        .ql-html-textarea { background: #1e293b; border-color: #334155; color: #e2e8f0; }
        .ql-toolbar .ql-stroke { stroke: #94a3b8; }
        .ql-toolbar .ql-fill { fill: #94a3b8; }
    }
</style>
@endonce

<script>
// ─── Global registry ─────────────────────────────────────────────────────────
window._quillInstances = window._quillInstances || {};

/**
 * setQuillContent(selector, html)
 * Injects HTML content into an already-initialised Quill editor.
 * Used by draftAI callbacks to push AI-generated content.
 */
function setQuillContent(selector, html) {
    var q = window._quillInstances[selector];
    if (!q) return;
    q.root.innerHTML = html || '';
    // Also update the source textarea if visible
    var wrapper = q.root.closest('.ql-wrapper');
    if (wrapper) {
        var srcTA = wrapper.querySelector('.ql-html-textarea');
        if (srcTA) srcTA.value = html || '';
    }
    // Sync to the hidden native textarea
    var ta = document.querySelector(selector);
    if (ta) {
        ta.value = (html === '<p><br></p>' || !html) ? '' : html;
        ta.dispatchEvent(new Event('input', { bubbles: true }));
    }
}

/**
 * initQuill(textareaSelector, height)
 * Mounts a rich Quill.js editor on an existing <textarea>.
 * Features:
 *   - Full toolbar (headings, bold, italic, lists, links, code, align, color)
 *   - HTML source toggle button (</>)
 *   - Sync on text-change and form submit
 *   - setQuillContent() for programmatic updates (draftAI)
 *
 * @param {string} textareaSelector  CSS selector of the textarea (e.g. '#notes')
 * @param {number} height            Editor height in px (default: 280)
 */
function initQuill(textareaSelector, height) {
    height = height || 280;

    var ta = document.querySelector(textareaSelector);
    if (!ta) { console.warn('initQuill: element not found:', textareaSelector); return; }

    // Skip if already initialised
    if (ta.dataset.quillReady) return;
    ta.dataset.quillReady = '1';

    // Hide the native textarea
    ta.style.display = 'none';

    // ── Wrapper ───────────────────────────────────────────────────────────────
    var wrapper = document.createElement('div');
    wrapper.className = 'ql-wrapper';
    ta.parentNode.insertBefore(wrapper, ta);
    wrapper.appendChild(ta); // move textarea inside wrapper

    // ── Editor div ────────────────────────────────────────────────────────────
    var editorDiv = document.createElement('div');
    editorDiv.style.height = height + 'px';
    wrapper.insertBefore(editorDiv, ta);

    // ── Source HTML textarea ──────────────────────────────────────────────────
    var srcTA = document.createElement('textarea');
    srcTA.className = 'ql-html-textarea';
    srcTA.style.minHeight = height + 'px';
    srcTA.spellcheck = false;
    wrapper.insertBefore(srcTA, ta);

    // ── Toolbar ───────────────────────────────────────────────────────────────
    var toolbarOptions = [
        [{ header: [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ color: [] }, { background: [] }],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ align: [] }],
        ['link', 'image', 'blockquote', 'code-block'],
        ['clean'],
        ['html-source']  // custom HTML source toggle
    ];

    var quill = new Quill(editorDiv, {
        theme: 'snow',
        placeholder: ta.placeholder || '',
        modules: {
            toolbar: {
                container: toolbarOptions,
                handlers: {
                    'html-source': function() {
                        toggleHTMLSource(quill, editorDiv, srcTA);
                    }
                }
            }
        }
    });

    // Register for external access
    window._quillInstances[textareaSelector] = quill;

    // ── Load initial value ────────────────────────────────────────────────────
    if (ta.value && ta.value.trim()) {
        quill.root.innerHTML = ta.value;
    }

    // ── Sync textarea ← Quill ─────────────────────────────────────────────────
    function syncToTextarea() {
        var html = quill.root.innerHTML;
        ta.value = (html === '<p><br></p>') ? '' : html;
        ta.dispatchEvent(new Event('input', { bubbles: true }));
        ta.dispatchEvent(new Event('change', { bubbles: true }));
    }
    quill.on('text-change', syncToTextarea);

    // ── Source textarea → Quill sync ──────────────────────────────────────────
    // If the user typed a full HTML document (<!DOCTYPE / <html>), bypass Quill
    // entirely and write straight to the hidden textarea — Quill only handles fragments.
    function isFullHtmlDoc(str) {
        return /^\s*<!DOCTYPE/i.test(str) || /^\s*<html/i.test(str);
    }

    function syncFromSource() {
        var val = srcTA.value;
        if (isFullHtmlDoc(val)) {
            // Full doc — store raw in hidden textarea, leave Quill editor untouched
            ta.value = val;
            ta.dispatchEvent(new Event('input',  { bubbles: true }));
            ta.dispatchEvent(new Event('change', { bubbles: true }));
        } else {
            quill.root.innerHTML = val;
            syncToTextarea();
        }
    }

    srcTA.addEventListener('input', syncFromSource);

    // ── Force sync on form submit ─────────────────────────────────────────────
    var form = ta.closest('form');
    if (form) {
        form.addEventListener('submit', function() {
            if (srcTA.style.display !== 'none') {
                syncFromSource();
            } else {
                syncToTextarea();
            }
        }, true);
    }
}

/**
 * Toggle between Quill editor and raw HTML source textarea
 */
function toggleHTMLSource(quill, editorDiv, srcTA) {
    var isSourceView = srcTA.style.display !== 'none';

    if (isSourceView) {
        // Switch back to visual editor
        // Only update Quill if the content is a fragment (not a full HTML doc)
        if (!isFullHtmlDoc(srcTA.value)) {
            quill.root.innerHTML = srcTA.value;
        }
        // Always update the hidden textarea with raw value
        var ta = srcTA.closest('.ql-wrapper').querySelector('textarea[data-quill-ready]');
        if (ta) {
            ta.value = srcTA.value;
            ta.dispatchEvent(new Event('input',  { bubbles: true }));
            ta.dispatchEvent(new Event('change', { bubbles: true }));
        }
        srcTA.style.display = 'none';
        editorDiv.style.display = '';
        var btn = editorDiv.closest('.ql-wrapper')?.querySelector('.ql-html-source');
        if (btn) btn.classList.remove('ql-active');
    } else {
        // Switch to HTML source view
        // Read from hidden textarea (truest value) not quill.root.innerHTML
        var ta = editorDiv.closest('.ql-wrapper').querySelector('textarea[data-quill-ready]');
        srcTA.value = ta ? ta.value : (quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML);
        editorDiv.style.display = 'none';
        srcTA.style.display = 'block';
        srcTA.focus();
        var btn = editorDiv.closest('.ql-wrapper')?.querySelector('.ql-html-source');
        if (btn) btn.classList.add('ql-active');
    }
}
</script>
