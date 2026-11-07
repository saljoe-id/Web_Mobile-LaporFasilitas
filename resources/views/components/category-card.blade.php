@props(['category'])

<a class="category-card" href="{{ route('complaints.create', ['category' => $category->id]) }}" aria-label="Buat pengaduan: {{ $category->name }}">
    <span class="category-icon" aria-hidden="true">
        @if (str_contains($category->slug, 'jalan'))
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 7h14M5 17h14M7 7l3 5-3 5m10-10-3 5 3 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @elseif (str_contains($category->slug, 'lampu') || str_contains($category->slug, 'penerangan'))
            <svg viewBox="0 0 24 24" fill="none"><path d="M8 14a6 6 0 1 1 8 0c-.8.7-1 1.3-1 2H9c0-.7-.2-1.3-1-2Zm2 5h4m-3-8 2 2m0-3-2 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @elseif (str_contains($category->slug, 'air') || str_contains($category->slug, 'pipa'))
            <svg viewBox="0 0 24 24" fill="none"><path d="M12 3s6 6.2 6 11a6 6 0 1 1-12 0c0-4.8 6-11 6-11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 15a3 3 0 0 0 3 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        @elseif (str_contains($category->slug, 'drainase') || str_contains($category->slug, 'saluran'))
            <svg viewBox="0 0 24 24" fill="none"><path d="M3 7c2.2 0 2.2 2 4.5 2S9.8 7 12 7s2.2 2 4.5 2S18.8 7 21 7M3 12c2.2 0 2.2 2 4.5 2s2.3-2 4.5-2 2.2 2 4.5 2 2.3-2 4.5-2M3 17c2.2 0 2.2 2 4.5 2s2.3-2 4.5-2 2.2 2 4.5 2 2.3-2 4.5-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        @elseif (str_contains($category->slug, 'taman') || str_contains($category->slug, 'ruang'))
            <svg viewBox="0 0 24 24" fill="none"><path d="M12 21v-8m0 2c-4 0-6-2-6-5 4 0 6 2 6 5Zm0-3c0-4 2-6 6-6 0 4-2 6-6 6Zm-5 9h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @else
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 20V8l8-4 8 4v12M8 20v-7h8v7M4 10h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @endif
    </span>
    <span class="category-copy"><strong>{{ $category->name }}</strong>@if ($category->description)<small>{{ $category->description }}</small>@endif</span>
    <span class="category-arrow" aria-hidden="true">›</span>
</a>
