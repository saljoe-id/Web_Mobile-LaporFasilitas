<x-layouts.staff title="Dashboard" active="Dashboard">
    <div class="staff-page-heading">
        <div><p class="eyebrow page-eyebrow">RUANG KERJA PETUGAS</p><h1>Selamat pagi, {{ auth()->user()->name }}.</h1><p>Operator fasilitas umum · {{ now()->translatedFormat('d M Y') }}</p></div>
        <span class="online-badge"><i></i>Aktif</span>
    </div>

    <section class="staff-metrics" aria-label="Ringkasan pengaduan">
        <article class="staff-metric"><span>Total pengaduan</span><strong>{{ number_format($complaintCount, 0, ',', '.') }}</strong><small>Seluruh laporan masuk</small></article>
        <article class="staff-metric"><span>Menunggu verifikasi</span><strong>{{ number_format($newCount, 0, ',', '.') }}</strong><small>Perlu pemeriksaan awal</small></article>
        <article class="staff-metric"><span>Sedang diproses</span><strong>{{ number_format($inProgressCount, 0, ',', '.') }}</strong><small>Dalam penanganan</small></article>
        <article class="staff-metric"><span>Selesai</span><strong>{{ number_format($completedCount, 0, ',', '.') }}</strong><small>Perbaikan dituntaskan</small></article>
    </section>

    <section class="staff-panel" aria-labelledby="recent-title">
        <div class="staff-panel-heading"><div><h2 id="recent-title">Pengaduan terbaru</h2><p>Laporan yang baru masuk ke sistem.</p></div><a href="{{ route('staff.complaints.index') }}">Lihat semua →</a></div>
        @forelse ($recentComplaints as $complaint)
            <article class="staff-report-row">
                <div class="staff-report-main"><small>{{ $complaint->complaint_code }} · {{ $complaint->created_at->translatedFormat('d M · H.i') }}</small><h3>{{ $complaint->title }}</h3><p>{{ $complaint->category->name }} · {{ $complaint->district }}, {{ $complaint->village }}</p></div>
                <span class="status-badge status-{{ $complaint->status->value }}">{{ $complaint->status->label() }}</span>
                <a class="staff-report-link" href="{{ route('staff.complaints.show', $complaint) }}">Detail <span aria-hidden="true">→</span></a>
            </article>
        @empty
            <div class="empty-state"><span class="empty-search-icon"><x-ui-icon name="clipboard" /></span><h3>Belum ada pengaduan</h3><p>Pengaduan baru akan tampil di sini setelah warga mengirim laporan.</p></div>
        @endforelse
    </section>

    <p class="notice notice-info staff-notice"><x-ui-icon name="shield" /><span>Data pelapor bersifat internal. Jangan menyalin informasi pribadi ke catatan publik.</span></p>
</x-layouts.staff>
