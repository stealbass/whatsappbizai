@if(Cookie::get('impersonator_id'))
    <a
        href="{{ route('impersonate.leave') }}"
        class="fifi-topbar-item inline-flex items-center justify-center gap-1 rounded-lg bg-primary-600 px-3 py-1.5 text-sm font-medium text-white shadow hover:bg-primary-500 focus:outline-none"
        title="Retour à l'espace administrateur"
    >
        <x-heroicon-o-arrow-left-on-rectangle class="h-4 w-4" />
        <span>Back to admin</span>
    </a>
@endif
