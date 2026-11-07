@props(['name'])

<svg class="icon" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
    @switch($name)
        @case('check')
            <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
            @break
        @case('plus-circle')
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 8v8m-4-4h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            @break
        @case('plus')
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 8v8m-4-4h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break
        @case('search')
            <circle cx="10.8" cy="10.8" r="6.3" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break
        @case('shield')
            <path d="M12 3 20 6v5c0 5-3.4 8.3-8 10-4.6-1.7-8-5-8-10V6l8-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('help')
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M9.5 9a2.5 2.5 0 1 1 4.2 1.8c-1 .8-1.7 1.2-1.7 2.7m0 3h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break
        @case('home')
            <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-6v-7h-4v7H4a1 1 0 0 1-1-1V10Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('error')
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 8v5m0 3h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            @break
        @case('location')
            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/>
            @break
        @case('map')
            <path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 3v15m6-12v15" stroke="currentColor" stroke-width="1.8"/>
            @break
        @case('image')
            <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/><circle cx="8.5" cy="9" r="1.5" stroke="currentColor" stroke-width="1.6"/><path d="m4 17 5-5 3 3 3-4 5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('send')
            <path d="m21 3-7.2 18-3.9-7.9L2 9.2 21 3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 13 21 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break
        @case('copy')
            <rect x="8" y="8" width="12" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2" stroke="currentColor" stroke-width="1.8"/>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break
        @case('building')
            <rect x="4" y="3" width="16" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 7h2m4 0h2M8 11h2m4 0h2M8 15h2m4 0h2m-5 6v-4h2v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break
        @case('dashboard')
            <rect x="3" y="3" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="13" y="3" width="8" height="5" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="13" y="10" width="8" height="11" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="3" y="13" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.8"/>
            @break
        @case('clipboard')
            <rect x="5" y="5" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M9 5V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1m-6 6h6m-6 4h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break
        @case('logout')
            <path d="M10 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h5m5-4 4-4-4-4m4 4H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            @break
    @endswitch
</svg>
