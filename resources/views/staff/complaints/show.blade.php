<x-layouts.staff title="Detail Pengaduan" active="Pengaduan">
    <a class="back-link" href="{{ route('staff.complaints.index') }}">← Manajemen pengaduan</a>
    <div class="staff-page-heading staff-detail-heading"><div><p class="eyebrow page-eyebrow">{{ $complaint->complaint_code }}</p><h1>{{ $complaint->title }}</h1><p>Dikirim {{ $complaint->created_at->translatedFormat('d M Y, H.i') }} WIB · {{ $complaint->category->name }}</p></div><span class="status-badge status-{{ $complaint->status->value }}">{{ $complaint->status->label() }}</span></div>

    @if (session('success'))<div class="notice notice-success staff-flash" role="status">{{ session('success') }}</div>@endif

    <div class="staff-detail-grid">
        <div class="staff-detail-main">
            @if ($complaint->photo_path)<section class="staff-panel"><h2>Foto bukti warga</h2><img class="detail-photo" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($complaint->photo_path) }}" alt="Foto kerusakan dari pelapor"></section>@endif
            <section class="staff-panel"><h2>Data pelapor · Internal</h2><p class="internal-data-note"><x-ui-icon name="shield" />Hanya petugas berwenang yang dapat melihat informasi ini.</p><dl class="detail-list"><div><dt>Nama pelapor</dt><dd>{{ $complaint->reporter_name }}</dd></div><div><dt>Nomor HP</dt><dd><a href="tel:{{ $complaint->reporter_phone }}">{{ $complaint->reporter_phone }}</a></dd></div></dl></section>
            <section class="staff-panel"><h2>Rincian kejadian</h2><dl class="detail-list"><div><dt>Kategori</dt><dd>{{ $complaint->category->name }}</dd></div><div><dt>Alamat</dt><dd>{{ $complaint->address }}</dd></div><div><dt>Wilayah</dt><dd>{{ $complaint->village }}, {{ $complaint->district }}</dd></div>@if($complaint->latitude && $complaint->longitude)<div><dt>Koordinat</dt><dd>{{ $complaint->latitude }}, {{ $complaint->longitude }}</dd></div>@endif<div><dt>Deskripsi</dt><dd>{{ $complaint->description }}</dd></div></dl></section>
            @if ($complaint->activeAssignment)<section class="staff-panel"><h2>Penugasan aktif</h2><p><strong>{{ $complaint->activeAssignment->user->name }}</strong> · {{ $complaint->activeAssignment->user->instansi }}</p><small class="field-help">Ditugaskan {{ $complaint->activeAssignment->assigned_at->translatedFormat('d M Y, H.i') }} WIB</small>@if($complaint->activeAssignment->notes)<p class="staff-private-note">{{ $complaint->activeAssignment->notes }}</p>@endif<a class="button button-secondary full-width" href="{{ route('staff.complaints.action', [$complaint, 'assign']) }}">Ubah Penugasan</a></section>@endif
        </div>

        <aside class="staff-detail-side">
            <section class="staff-panel staff-actions"><h2>Tindakan petugas</h2>
                @if (in_array($complaint->status, [\App\Enums\ComplaintStatus::Pending, \App\Enums\ComplaintStatus::Baru], true))
                    <a class="button button-primary full-width" href="{{ route('staff.complaints.action', [$complaint, 'verify']) }}">Verifikasi Pengaduan</a>
                @endif
                @if (in_array($complaint->status, [\App\Enums\ComplaintStatus::Pending, \App\Enums\ComplaintStatus::Baru, \App\Enums\ComplaintStatus::Diverifikasi, \App\Enums\ComplaintStatus::Diproses], true))
                    <a class="button button-danger-outline full-width" href="{{ route('staff.complaints.action', [$complaint, 'reject']) }}">Tolak Pengaduan</a>
                @endif
                @if ($complaint->status === \App\Enums\ComplaintStatus::Diverifikasi)
                    <a class="button button-secondary full-width" href="{{ route('staff.complaints.action', [$complaint, 'assign']) }}">Tugaskan Petugas</a>
                    @if ($complaint->activeAssignment)<a class="button button-primary full-width" href="{{ route('staff.complaints.action', [$complaint, 'progress']) }}">Mulai Penanganan</a>@endif
                @endif
                @if ($complaint->status === \App\Enums\ComplaintStatus::Diproses)
                    <a class="button button-primary full-width" href="{{ route('staff.complaints.action', [$complaint, 'complete']) }}">Tandai Selesai</a>
                @endif
                @if (in_array($complaint->status, [\App\Enums\ComplaintStatus::Selesai, \App\Enums\ComplaintStatus::Ditolak], true))<p class="field-help">Tidak ada tindakan lanjutan untuk status ini.</p>@endif
            </section>
            <section class="staff-panel timeline-card"><h2>Riwayat yang terlihat warga</h2><ol class="timeline">@foreach($complaint->statusHistories as $history)<li class="timeline-item {{ $loop->last ? 'is-current' : 'is-done' }}"><span class="timeline-dot" aria-hidden="true"></span><div><strong>{{ $history->new_status->label() }}</strong><small>{{ $history->created_at->translatedFormat('d M Y, H.i') }} WIB · {{ $history->user?->name ?? 'Sistem' }}</small></div></li>@endforeach</ol></section>
        </aside>
    </div>
</x-layouts.staff>
