<x-layouts.public title="Detail Pengaduan — FasilitasKita" active="Lacak">
    <div class="tracking-detail section-wrap">
        <a class="back-link" href="{{ route('tracking.index', ['code' => $complaint->complaint_code]) }}">← Kembali ke hasil pelacakan</a>
        <p class="eyebrow page-eyebrow">{{ $complaint->complaint_code }}</p>
        <h1 class="page-title">{{ $complaint->title }}</h1>
        <div class="status-line"><span class="status-badge status-{{ $complaint->status->value }}">{{ $complaint->status->label() }}</span><small>Dikirim {{ $complaint->created_at->translatedFormat('d M Y, H.i') }} WIB</small></div>
        <div class="status-summary" aria-label="Keterangan perbaikan">
            <strong>{{ $statusSummary['repair_label'] }}</strong>
            <span>{{ $statusSummary['duration_label'] }}. Status diperbarui {{ $statusSummary['updated_at']->translatedFormat('d M Y, H.i') }} WIB.</span>
        </div>

        @if ($complaint->photo_path)
            <img class="detail-photo" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($complaint->photo_path) }}" alt="Foto fasilitas yang dilaporkan">
        @endif

        <section class="detail-card" aria-labelledby="public-info-title">
            <h2 id="public-info-title">Informasi pengaduan</h2>
            <dl class="detail-list">
                <div><dt>Kategori</dt><dd>{{ $complaint->category->name }}</dd></div>
                <div><dt>Alamat kejadian</dt><dd>{{ $complaint->address }}</dd></div>
                <div><dt>Wilayah</dt><dd>{{ $complaint->village }}, {{ $complaint->district }}</dd></div>
                <div><dt>Deskripsi</dt><dd>{{ $complaint->description }}</dd></div>
            </dl>
        </section>

        <section class="detail-card timeline-card" aria-labelledby="timeline-title">
            <h2 id="timeline-title">Status & riwayat penanganan</h2>
            <ol class="timeline">
                @foreach ($complaint->statusHistories as $history)
                    <li class="timeline-item {{ $loop->last ? 'is-current' : 'is-done' }}">
                        <span class="timeline-dot" aria-hidden="true"></span>
                        <div><strong>{{ $history->new_status->label() }}</strong><small>{{ $history->created_at->translatedFormat('d M Y, H.i') }} WIB</small>@if($history->notes)<p class="timeline-note">{{ $history->notes }}</p>@endif</div>
                    </li>
                @endforeach
            </ol>
            <p class="privacy-hint">Riwayat ini hanya menampilkan status penanganan. Data pribadi pelapor hanya dapat dilihat petugas.</p>
        </section>

        <div class="notice notice-info"><x-ui-icon name="shield" /><p>Identitas pelapor dilindungi. Halaman ini menampilkan informasi fasilitas dan penanganannya.</p></div>
        <button type="button" class="button button-secondary full-width" data-copy-button="{{ $complaint->complaint_code }}"><x-ui-icon name="copy" /><span data-copy-label>Salin ID Pengaduan</span></button>
    </div>
</x-layouts.public>
