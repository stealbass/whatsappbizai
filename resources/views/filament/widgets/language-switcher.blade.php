<div style="display:flex;gap:4px;align-items:center;margin-right:12px;">
    <a href="{{ url('admin/language/fr') }}"
       style="padding:4px 10px;border-radius:6px;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s;{{ app()->getLocale() === 'fr' ? 'background:#0ea5e9;color:#fff;' : 'background:rgba(255,255,255,.1);color:#94a3b8;' }}">
        FR
    </a>
    <a href="{{ url('admin/language/en') }}"
       style="padding:4px 10px;border-radius:6px;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s;{{ app()->getLocale() === 'en' ? 'background:#0ea5e9;color:#fff;' : 'background:rgba(255,255,255,.1);color:#94a3b8;' }}">
        EN
    </a>
</div>
