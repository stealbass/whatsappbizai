<style>
.html-editor-wrap { border:1px solid #d1d5db; border-radius:8px; overflow:hidden; background:#fff; margin-bottom:16px; }
.html-editor-tabs { display:flex; background:#f1f5f9; border-bottom:1px solid #d1d5db; }
.html-editor-tab { padding:8px 16px; font-size:13px; font-weight:600; cursor:pointer; border:none; background:none; color:#64748b; }
.html-editor-tab.active { background:#fff; color:#0ea5e9; border-bottom:2px solid #0ea5e9; }
.html-editor-source { width:100%; min-height:200px; font-family:monospace; font-size:13px; border:none; padding:12px; resize:vertical; background:#1e293b; color:#e2e8f0; }
.html-editor-preview { width:100%; min-height:200px; border:none; display:none; background:#fff; }
.html-editor-preview.active { display:block; }
.html-editor-wrap iframe { width:100%; min-height:300px; border:none; }
.html-editor-label { font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; display:block; }
</style>

<div id="legalHtmlEditors">
    @foreach(['privacy_policy' => 'Politique de confidentialité', 'terms_conditions' => 'Conditions générales', 'cookie_policy' => 'Politique de cookies'] as $field => $label)
    <div style="margin-bottom:16px;">
        <label class="html-editor-label">{{ $label }}</label>
        <div class="html-editor-wrap" data-field="{{ $field }}">
            <div class="html-editor-tabs">
                <button type="button" class="html-editor-tab active" onclick="htmlEditorSwitchTab(this, 'source')">📝 Code source</button>
                <button type="button" class="html-editor-tab" onclick="htmlEditorSwitchTab(this, 'preview')">👁 Aperçu</button>
            </div>
            <textarea class="html-editor-source" data-html-source="{{ $field }}" placeholder="Collez votre code HTML ici..."></textarea>
            <div class="html-editor-preview">
                <iframe data-html-preview="{{ $field }}"></iframe>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
function htmlEditorSwitchTab(el, tab) {
    const wrap = el.closest('.html-editor-wrap');
    const field = wrap.getAttribute('data-field');
    wrap.querySelectorAll('.html-editor-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    const source = wrap.querySelector('[data-html-source]');
    const previewWrap = wrap.querySelector('.html-editor-preview');
    if (tab === 'preview') {
        wrap.querySelector('[data-html-preview]').srcdoc = source.value;
        previewWrap.classList.add('active');
        source.style.display = 'none';
    } else {
        previewWrap.classList.remove('active');
        source.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.querySelectorAll('[data-html-editor]').forEach(function(wrap) {
            const field = wrap.getAttribute('data-field');
            const source = wrap.querySelector('[data-html-source]');
            const targetField = document.querySelector('[wire\\:model*="' + field + '"]') || document.querySelector('input[name*="' + field + '"], textarea[name*="' + field + '"]');
            if (targetField && source) {
                source.value = targetField.value || '';
                source.addEventListener('input', function() {
                    targetField.value = source.value;
                    targetField.dispatchEvent(new Event('input', { bubbles: true }));
                    targetField.dispatchEvent(new Event('change', { bubbles: true }));
                });
                const observer = new MutationObserver(function() {
                    if (targetField.value !== source.value) {
                        source.value = targetField.value;
                    }
                });
                observer.observe(targetField, { attributes: true, attributeFilter: ['value'] });
            }
        });
    }, 500);
});
</script>
