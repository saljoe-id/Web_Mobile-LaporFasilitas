<x-layouts.public title="Pengaduan Terkirim — FasilitasKita" active="Lapor">
    <section class="success-page section-wrap" aria-labelledby="success-title">
        <span class="success-mark" aria-hidden="true"><x-ui-icon name="check" /></span>
        <p class="eyebrow page-eyebrow">PENGADUAN BERHASIL DIKIRIM</p>
        <h1 class="page-title" id="success-title">Terima kasih sudah peduli.</h1>
        <p class="page-intro">Laporan Anda sudah diterima dan akan diperiksa oleh petugas.</p>
        <div class="code-card">
            <small>ID PENGADUAN ANDA</small>
            <strong data-copy-value="{{ $complaint->complaint_code }}">{{ $complaint->complaint_code }}</strong>
            <button type="button" class="button button-secondary full-width" data-copy-button="{{ $complaint->complaint_code }}"><x-ui-icon name="copy" /><span data-copy-label>Salin ID Pengaduan</span></button>
            <p class="code-status"><x-ui-icon name="clock" />{{ $complaint->status->label() }}</p>
        </div>
        <div class="notice notice-info"><x-ui-icon name="shield" /><p>Simpan ID ini untuk melihat status dan catatan penanganan. Identitas pelapor tidak ditampilkan pada halaman publik.</p></div>
        <a class="button button-primary full-width" href="{{ route('tracking.show', $complaint->complaint_code) }}"><x-ui-icon name="search" />Lacak Pengaduan</a>
        <a class="button button-secondary full-width" href="{{ route('landing') }}">Kembali ke Beranda</a>
    </section>
</x-layouts.public>
