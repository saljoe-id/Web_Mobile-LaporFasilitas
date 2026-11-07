@props(['title', 'active' => 'Dashboard'])

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f5faff">
        <title>{{ $title }} — Portal Petugas FasilitasKita</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="staff-body">
        <a class="skip-link" href="#staff-content">Lewati ke isi utama</a>
        <div class="staff-app">
            <header class="staff-topbar">
                <a class="brand" href="{{ route('staff.dashboard') }}" aria-label="FasilitasKita, Dashboard petugas">
                    <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 40 40" fill="none"><rect x="3" y="3" width="34" height="34" rx="10" fill="currentColor"/><path d="M12 29V13h16v16M16 29v-7h8v7M10 17h20M16 17v-4m8 4v-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <span class="brand-copy"><strong>FasilitasKita</strong><small>PORTAL INTERNAL PETUGAS</small></span>
                </a>
                <a class="internal-link" href="{{ route('landing') }}">Beranda <span aria-hidden="true">↗</span></a>
            </header>
            <aside class="staff-sidebar" aria-label="Menu petugas">
                <p class="staff-menu-label">RUANG KERJA</p>
                <a @class(['staff-menu-link', 'is-active' => $active === 'Dashboard']) href="{{ route('staff.dashboard') }}"><x-ui-icon name="dashboard" />Dashboard</a>
                <a @class(['staff-menu-link', 'is-active' => $active === 'Pengaduan']) href="{{ route('staff.complaints.index') }}"><x-ui-icon name="clipboard" />Pengaduan</a>
                <div class="staff-sidebar-note"><x-ui-icon name="shield" /><span>Akses terbatas. Data pribadi pelapor hanya untuk tindak lanjut internal.</span></div>
                <form action="{{ route('staff.logout') }}" method="POST" class="staff-logout-form">@csrf<button type="submit"><x-ui-icon name="logout" />Keluar dari portal</button></form>
            </aside>
            <main class="staff-content" id="staff-content">
                @if (session('success'))<div class="notice notice-success staff-flash" role="status">{{ session('success') }}</div>@endif
                {{ $slot }}
                <footer class="staff-footer">Akses terbatas · Dinas Pekerjaan Umum Kota Bandung</footer>
            </main>
            <nav class="staff-mobile-nav" aria-label="Navigasi petugas">
                <a @class(['is-active' => $active === 'Dashboard']) href="{{ route('staff.dashboard') }}"><x-ui-icon name="dashboard" /><span>Dashboard</span></a>
                <a @class(['is-active' => $active === 'Pengaduan']) href="{{ route('staff.complaints.index') }}"><x-ui-icon name="clipboard" /><span>Pengaduan</span></a>
                <form action="{{ route('staff.logout') }}" method="POST">@csrf<button type="submit"><x-ui-icon name="logout" /><span>Keluar</span></button></form>
            </nav>
        </div>
    </body>
</html>
