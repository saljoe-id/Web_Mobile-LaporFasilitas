@props(['title' => 'FasilitasKita — Layanan Fasilitas Umum Kota Bandung', 'active' => 'Beranda', 'mapAssets' => false])

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f5faff">
        <meta name="description" content="Laporkan kerusakan fasilitas umum Kota Bandung dan pantau penanganannya.">
        <title>{{ $title }}</title>
        @if ($mapAssets)
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="" defer data-leaflet-script></script>
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <a class="skip-link" href="#konten">Lewati ke isi utama</a>
        <div class="page-shell" id="beranda">
            <x-site-header />
            <main id="konten">{{ $slot }}</main>
            <footer class="site-footer" id="footer">
                <p><strong>FasilitasKita</strong> · Layanan resmi Kota Bandung<br>Laporan warga, ruang publik lebih terawat.</p>
            </footer>
            <x-bottom-navigation :active="$active" />
        </div>
    </body>
</html>
