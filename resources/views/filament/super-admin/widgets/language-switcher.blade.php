@php
    $currentLocale = app()->getLocale();
@endphp

<div x-data="{ open: false }" class="relative">
    <button
        @click="open = !open"
        class="flex items-center gap-1 px-3 py-1.5 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
        title="Changer de langue"
    >
        <span class="text-lg">{{ $currentLocale === 'fr' ? '🇫🇷' : '🇬🇧' }}</span>
        <span class="hidden sm:inline">{{ strtoupper($currentLocale) }}</span>
        <x-heroicon-o-chevron-down class="w-3 h-3" />
    </button>

    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="absolute right-0 top-full mt-1 w-40 bg-white dark:bg-gray-900 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
    >
        <a
            href="?locale=fr"
            class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-t-lg {{ $currentLocale === 'fr' ? 'bg-gray-50 dark:bg-gray-800 font-semibold' : '' }}"
        >
            🇫🇷 Français
        </a>
        <a
            href="?locale=en"
            class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-b-lg {{ $currentLocale === 'en' ? 'bg-gray-50 dark:bg-gray-800 font-semibold' : '' }}"
        >
            🇬🇧 English
        </a>
    </div>
</div>
