<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f5faff">
        <title>Masuk Petugas — FasilitasKita</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="staff-auth-body">
        <a class="skip-link" href="#login-form">Lewati ke formulir masuk</a>
        <header class="staff-topbar auth-topbar">
            <a class="brand" href="{{ route('landing') }}">
                <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 40 40" fill="none"><rect x="3" y="3" width="34" height="34" rx="10" fill="currentColor"/><path d="M12 29V13h16v16M16 29v-7h8v7M10 17h20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <span class="brand-copy"><strong>FasilitasKita</strong><small>PORTAL INTERNAL PETUGAS</small></span>
            </a>
            <a class="internal-link" href="{{ route('landing') }}">Beranda</a>
        </header>
        <main class="staff-login-wrap" id="login-form">
            <section class="staff-login-card" aria-labelledby="login-title">
                <span class="login-shield"><x-ui-icon name="shield" /></span>
                <p class="eyebrow page-eyebrow">KHUSUS PETUGAS</p>
                <h1 id="login-title">Masuk ke ruang kerja</h1>
                <p class="page-intro">Akses penanganan pengaduan untuk pegawai yang berwenang.</p>
                <div class="notice notice-info"><x-ui-icon name="shield" /><p>Warga tidak perlu masuk. Gunakan halaman Lacak untuk melihat status pengaduan.</p></div>
                <form action="{{ route('staff.login.store') }}" method="POST" class="staff-login-form">
                    @csrf
                    <div class="field"><label for="email">Email kedinasan <span>*</span></label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>@error('email')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="field"><label for="password">Kata sandi <span>*</span></label><input id="password" type="password" name="password" autocomplete="current-password" required>@error('password')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <button class="button button-primary full-width" type="submit">Masuk sebagai Petugas</button>
                </form>
                <a class="button button-secondary full-width" href="{{ route('landing') }}">Kembali ke Website Warga</a>
                <p class="staff-auth-footnote">Akses terbatas · Data pelapor dijaga untuk keperluan penanganan.</p>
            </section>
        </main>
    </body>
</html>
