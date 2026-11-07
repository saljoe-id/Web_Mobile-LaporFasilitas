<x-layouts.public title="FasilitasKita — Layanan Fasilitas Umum Kota Bandung">
    <section class="hero section-wrap" aria-labelledby="hero-title">
        <p class="eyebrow"><x-ui-icon name="check" />Layanan publik, tanpa akun</p>
        <h1 id="hero-title">Ada yang rusak?<br>Mari perbaiki bersama.</h1>
        <p class="hero-copy">Laporkan kerusakan fasilitas umum di Kota Bandung. Pantau penanganannya, dari laporan hingga perbaikan.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="{{ route('complaints.create') }}"><x-ui-icon name="plus-circle" />Buat Pengaduan</a>
            <a class="button button-secondary" href="{{ route('tracking.index') }}"><x-ui-icon name="search" />Lacak Pengaduan</a>
        </div>
        <p class="privacy-note"><x-ui-icon name="shield" />Tanpa login. Identitas Anda tetap terlindungi.</p>
    </section>

    <section class="stats section-wrap" aria-label="Ringkasan pengaduan">
        <div class="stat-card">
            <x-stat-value :value="$complaintCount" label="Total laporan" />
            <x-stat-value :value="$inProgressCount" label="Diproses" />
            <x-stat-value :value="$completedCount" label="Selesai" />
        </div>
    </section>

    <section class="trend-section section-wrap" aria-labelledby="trend-title">
        <div class="trend-heading">
            <div>
                <h2 id="trend-title">Tren penanganan aduan bulanan</h2>
                <p class="section-copy">Jumlah laporan masuk dan laporan yang selesai dalam 12 bulan terakhir.</p>
            </div>
            <a class="trend-map-link" href="{{ route('map.index') }}"><x-ui-icon name="map" />Lihat peta laporan</a>
        </div>

        <div class="trend-card">
            <div class="trend-legend" aria-label="Keterangan grafik">
                <span><i class="legend-dot legend-reported"></i>Laporan masuk</span>
                <span><i class="legend-dot legend-resolved"></i>Selesai diperbaiki</span>
            </div>
            @if ($hasMonthlyData)
                <div class="trend-chart" role="img" aria-label="Grafik batang laporan masuk dan selesai per bulan selama 12 bulan terakhir">
                    <div class="trend-axis" aria-hidden="true"><span>{{ $chartMaximum }}</span><span>{{ (int) round($chartMaximum / 2) }}</span><span>0</span></div>
                    @foreach ($monthlyTrend as $month)
                        <div class="trend-month" title="{{ $month['full_label'] }}: {{ $month['reported'] }} laporan masuk, {{ $month['resolved'] }} selesai">
                            <div class="trend-bars">
                                <i class="trend-bar bar-reported" style="--bar-height: {{ $month['reported_height'] }}%"></i>
                                <i class="trend-bar bar-resolved" style="--bar-height: {{ $month['resolved_height'] }}%"></i>
                            </div>
                            <span>{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="trend-empty">Belum ada laporan dalam 12 bulan terakhir.</p>
            @endif
            <table class="visually-hidden">
                <caption>Data tren penanganan aduan bulanan</caption>
                <thead><tr><th>Bulan</th><th>Laporan masuk</th><th>Selesai diperbaiki</th></tr></thead>
                <tbody>
                    @foreach ($monthlyTrend as $month)
                        <tr><th>{{ $month['full_label'] }}</th><td>{{ $month['reported'] }}</td><td>{{ $month['resolved'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <p class="trend-footnote">Data dihitung dari laporan yang tersimpan dan waktu status selesai dicatat.</p>
        </div>
    </section>

    <section class="steps-section section-wrap" id="alur" aria-labelledby="steps-title">
        <h2 id="steps-title">Tiga langkah untuk ruang lebih baik</h2>
        <ol class="steps-list">
            <li class="step-item"><span class="step-number">1</span><div><h3>Kirim laporan</h3><p>Isi lokasi, ceritakan kerusakan, lalu unggah foto yang jelas.</p></div></li>
            <li class="step-item"><span class="step-number">2</span><div><h3>Simpan ID pengaduan</h3><p>Gunakan ID unik untuk melihat status, tanpa perlu membuat akun.</p></div></li>
            <li class="step-item"><span class="step-number">3</span><div><h3>Pantau perbaikan</h3><p>Baca catatan petugas dan lihat bukti ketika penanganan selesai.</p></div></li>
        </ol>
    </section>

    <aside class="emergency-note section-wrap" aria-labelledby="emergency-title">
        <h2 id="emergency-title">Untuk fasilitas, bukan keadaan darurat</h2>
        <p>Ada bahaya langsung bagi keselamatan? Hubungi layanan darurat 112. Bantuan FasilitasKita tersedia Senin–Jumat, 08.00–16.00 WIB.</p>
        <a href="{{ route('staff.login') }}">Akses petugas · Khusus pegawai berwenang <span aria-hidden="true">→</span></a>
    </aside>
</x-layouts.public>
