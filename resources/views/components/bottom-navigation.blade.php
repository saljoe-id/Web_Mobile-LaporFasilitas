@props(['active' => 'Beranda'])

<nav class="bottom-navigation" aria-label="Navigasi utama">
    <a @class(['bottom-nav-item', 'is-active' => $active === 'Beranda']) href="{{ route('landing') }}" @if($active === 'Beranda') aria-current="page" @endif><x-ui-icon name="home" /><span>Beranda</span></a>
    <a @class(['bottom-nav-item', 'is-active' => $active === 'Lapor']) href="{{ route('complaints.create') }}" @if($active === 'Lapor') aria-current="page" @endif><x-ui-icon name="plus" /><span>Lapor</span></a>
    <a @class(['bottom-nav-item', 'is-active' => $active === 'Peta']) href="{{ route('map.index') }}" @if($active === 'Peta') aria-current="page" @endif><x-ui-icon name="map" /><span>Peta</span></a>
    <a @class(['bottom-nav-item', 'is-active' => $active === 'Lacak']) href="{{ route('tracking.index') }}" @if($active === 'Lacak') aria-current="page" @endif><x-ui-icon name="search" /><span>Lacak</span></a>
</nav>
