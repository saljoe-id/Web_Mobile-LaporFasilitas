<x-layouts.public title="Lacak Pengaduan — FasilitasKita" active="Lacak">
    <div class="tracking-page section-wrap">
        <p class="eyebrow page-eyebrow">TANPA LOGIN</p>
        <h1 class="page-title">Lacak pengaduan</h1>
        <p class="page-intro">Cukup gunakan ID pengaduan. Tidak perlu nama, nomor HP, atau akun.</p>

        <form class="tracking-form" action="{{ route('tracking.index') }}" method="GET">
            <label for="code">ID pengaduan</label>
            <input id="code" name="code" value="{{ $code }}" placeholder="Contoh: ADM-20261107-ABC123" autocomplete="off" maxlength="20">
            @error('code')<small class="field-error">{{ $message }}</small>@enderror
            <button class="button button-primary full-width" type="submit"><x-ui-icon name="search" />Cari Pengaduan</button>
            <small class="field-help">ID ada pada halaman pengaduan berhasil dikirim.</small>
        </form>

        @if ($code !== '' && ! $complaint)
            <div class="notice notice-error tracking-feedback" role="alert"><x-ui-icon name="error" /><div><strong>Pengaduan belum ditemukan</strong><p>Periksa ID yang Anda masukkan. Jika baru mengirim laporan, tunggu beberapa saat lalu coba lagi.</p></div></div>
        @elseif ($complaint)
            <section class="result-section" aria-labelledby="result-title">
                <h2 id="result-title">Pengaduan ditemukan</h2>
                <article class="result-card">
                    <div class="result-top"><small>{{ $complaint->complaint_code }}</small><span class="status-badge status-{{ $complaint->status->value }}">{{ $complaint->status->label() }}</span></div>
                    <h3>{{ $complaint->title }}</h3>
                    <p>{{ $complaint->category->name }} · {{ $complaint->district }}, {{ $complaint->village }}</p>
                    <small class="field-help">Dikirim {{ $complaint->created_at->translatedFormat('d M Y, H.i') }} WIB</small>
                    <div class="status-summary" aria-label="Keterangan perbaikan">
                        <strong>{{ $statusSummary['repair_label'] }}</strong>
                        <span>{{ $statusSummary['duration_label'] }}. Status diperbarui {{ $statusSummary['updated_at']->translatedFormat('d M Y, H.i') }} WIB.</span>
                    </div>
                    <a class="result-link" href="{{ route('tracking.show', $complaint->complaint_code) }}">Lihat detail <span aria-hidden="true">→</span></a>
                </article>
            </section>
        @else
            <div class="tracking-empty"><span class="empty-search-icon"><x-ui-icon name="search" /></span><h2>Sudah punya ID pengaduan?</h2><p>Masukkan ID untuk melihat status dan catatan publik penanganan laporan Anda.</p></div>
        @endif
    </div>
</x-layouts.public>
