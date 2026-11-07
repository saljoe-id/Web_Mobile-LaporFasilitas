<x-layouts.staff title="Manajemen Pengaduan" active="Pengaduan">
    <div class="staff-page-heading"><div><p class="eyebrow page-eyebrow">PORTAL PETUGAS</p><h1>Manajemen pengaduan</h1><p>Temukan laporan, periksa detail, dan lanjutkan penanganan.</p></div></div>

    <form class="staff-search" action="{{ route('staff.complaints.index') }}" method="GET">
        <div class="field"><label for="q">Cari ID atau judul</label><input id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Contoh: ADM-20261107-ABC123"></div>
        <div class="field"><label for="status">Status</label><select id="status" name="status"><option value="">Semua status</option>@foreach (\App\Enums\ComplaintStatus::cases() as $status)<option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>@endforeach</select></div>
        <div class="field"><label for="category">Kategori</label><select id="category" name="category"><option value="">Semua kategori</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <button class="button button-primary" type="submit"><x-ui-icon name="search" />Cari</button>
    </form>

    <section class="staff-panel" aria-label="Daftar pengaduan">
        <div class="staff-panel-heading"><div><h2>{{ number_format($complaints->total(), 0, ',', '.') }} pengaduan</h2><p>Gunakan filter untuk mempersempit daftar.</p></div></div>
        @forelse ($complaints as $complaint)
            <article class="staff-report-row">
                <div class="staff-report-main"><small>{{ $complaint->complaint_code }} · {{ $complaint->created_at->translatedFormat('d M Y · H.i') }}</small><h3>{{ $complaint->title }}</h3><p>{{ $complaint->category->name }} · {{ $complaint->district }}, {{ $complaint->village }}</p></div>
                <span class="status-badge status-{{ $complaint->status->value }}">{{ $complaint->status->label() }}</span>
                <a class="staff-report-link" href="{{ route('staff.complaints.show', $complaint) }}">Lihat detail <span aria-hidden="true">→</span></a>
            </article>
        @empty
            <div class="empty-state"><h3>Tidak ada pengaduan yang cocok</h3><p>Coba ubah kata kunci atau filter status.</p></div>
        @endforelse
        <div class="staff-pagination">{{ $complaints->links() }}</div>
    </section>
</x-layouts.staff>
