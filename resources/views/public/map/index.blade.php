<x-layouts.public title="Peta Pengaduan — FasilitasKita" active="Peta" :map-assets="true">
    <section class="map-page section-wrap" aria-labelledby="map-title">
        <p class="eyebrow page-eyebrow"><x-ui-icon name="location" />PETA LAPORAN WARGA</p>
        <h1 class="page-title" id="map-title">Kerusakan di sekitar kita</h1>
        <p class="page-intro">Pilih pin untuk melihat keterangan lokasi, status perbaikan, dan lama penanganan.</p>

        <div class="map-summary" aria-live="polite">
            <strong><span data-report-count>Memuat…</span> laporan di peta</strong>
            <span>Hanya lokasi yang memiliki titik koordinat ditampilkan.</span>
        </div>
        <p class="map-empty" data-map-empty hidden>Belum ada laporan dengan koordinat yang dapat ditampilkan. Laporan selesai yang melewati 15 hari tidak ditampilkan.</p>

        <div class="map-card">
            <div id="public-report-map" data-public-report-map data-endpoint="{{ route('map.data') }}" role="region" aria-label="Peta lokasi pengaduan fasilitas umum"></div>
            <p class="map-error" data-map-error role="status" hidden>Peta belum dapat dimuat. Periksa koneksi internet lalu coba lagi.</p>
        </div>

        <div class="map-legend" aria-label="Keterangan warna pin">
            <span><i class="legend-dot legend-pending"></i>Belum diperbaiki</span>
            <span><i class="legend-dot legend-working"></i>Sedang ditangani</span>
            <span><i class="legend-dot legend-completed"></i>Sudah diperbaiki</span>
        </div>

        <p class="map-retention-note"><x-ui-icon name="clock" />Laporan selesai tetap terlihat selama 15 hari sejak dinyatakan selesai, lalu pin dihapus dari peta.</p>
        <p class="map-credit">Peta © <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap contributors</a></p>
    </section>
</x-layouts.public>
