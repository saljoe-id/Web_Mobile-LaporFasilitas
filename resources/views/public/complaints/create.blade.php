<x-layouts.public title="Buat Pengaduan — FasilitasKita" active="Lapor" mapAssets>
    <div class="form-page section-wrap">
        <p class="eyebrow page-eyebrow">LAPORAN WARGA</p>
        <h1 class="page-title">Buat pengaduan</h1>
        <p class="page-intro">Hal kecil yang Anda laporkan bisa membuat ruang bersama lebih aman.</p>

        <div class="notice notice-info"><x-ui-icon name="shield" /><p>Tidak perlu akun. Nama dan nomor HP hanya digunakan petugas untuk verifikasi, tidak ditampilkan ke publik.</p></div>
        <p class="required-note"><span aria-hidden="true">*</span> Wajib diisi · Satu formulir untuk satu lokasi kerusakan.</p>

        @if ($errors->any())
            <div class="notice notice-error" role="alert">
                <x-ui-icon name="error" />
                <div><strong>Periksa isian Anda</strong><p>{{ $errors->count() }} bagian perlu diperbaiki. Perbaiki kolom bertanda merah, lalu kirim kembali.</p></div>
            </div>
        @endif

        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" class="complaint-form" data-review-form>
            @csrf
            <section class="form-card" aria-labelledby="reporter-title">
                <h2 id="reporter-title">1. Data pelapor</h2>
                <div class="field">
                    <label for="reporter_name">Nama pelapor <span>*</span></label>
                    <input id="reporter_name" name="reporter_name" value="{{ old('reporter_name') }}" autocomplete="name" maxlength="100" required @error('reporter_name') aria-invalid="true" @enderror>
                    @error('reporter_name')<small class="field-error" id="reporter_name-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <label for="reporter_phone">Nomor HP <span>*</span></label>
                    <input id="reporter_phone" name="reporter_phone" type="tel" inputmode="numeric" autocomplete="tel" pattern="08[0-9]{8,11}" maxlength="13" value="{{ old('reporter_phone') }}" placeholder="08xx xxxx xxxx" required @error('reporter_phone') aria-invalid="true" @enderror aria-describedby="reporter_phone-help">
                    <small id="reporter_phone-help" class="field-help">Nomor aktif untuk menghubungi Anda jika diperlukan.</small>
                    @error('reporter_phone')<small class="field-error" id="reporter_phone-error">{{ $message }}</small>@enderror
                </div>
                <p class="privacy-hint">Identitas pelapor dilindungi. Data pada halaman publik tidak menampilkan kontak Anda.</p>
            </section>

            <section class="form-card" aria-labelledby="damage-title">
                <h2 id="damage-title">2. Ceritakan kerusakan</h2>
                <div class="field">
                    <label for="category_id">Jenis fasilitas <span>*</span></label>
                    <select id="category_id" name="category_id" required @error('category_id') aria-invalid="true" @enderror>
                        <option value="">Pilih jenis fasilitas</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $selectedCategory) === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<small class="field-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <label for="title">Judul pengaduan <span>*</span></label>
                    <input id="title" name="title" value="{{ old('title') }}" maxlength="150" required @error('title') aria-invalid="true" @enderror>
                    <small class="field-help">Judul singkat tentang kerusakan yang terlihat.</small>
                    @error('title')<small class="field-error">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <label for="description">Deskripsi <span>*</span></label>
                    <textarea id="description" name="description" rows="5" minlength="20" maxlength="5000" required data-character-count @error('description') aria-invalid="true" @enderror>{{ old('description') }}</textarea>
                    <small class="field-help"><span data-count>0</span>/5000 karakter · Jelaskan kondisi dan dampaknya.</small>
                    @error('description')<small class="field-error">{{ $message }}</small>@enderror
                </div>
            </section>

            <section class="form-card" aria-labelledby="location-title">
                <h2 id="location-title">3. Lokasi kejadian</h2>
                <div class="field">
                    <label for="address">Alamat / patokan <span>*</span></label>
                    <input id="address" name="address" value="{{ old('address') }}" maxlength="255" placeholder="Nama jalan, nomor, atau patokan" required @error('address') aria-invalid="true" @enderror>
                    @error('address')<small class="field-error">{{ $message }}</small>@enderror
                </div>
                <div class="field-row">
                    <div class="field"><label for="district">Kecamatan <span>*</span></label><input id="district" name="district" value="{{ old('district') }}" required @error('district') aria-invalid="true" @enderror>@error('district')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="field"><label for="village">Kelurahan <span>*</span></label><input id="village" name="village" value="{{ old('village') }}" required @error('village') aria-invalid="true" @enderror>@error('village')<small class="field-error">{{ $message }}</small>@enderror</div>
                </div>
                <button class="button button-secondary full-width location-button" type="button" data-location-button data-location-endpoint="{{ route('location.reverse') }}"><x-ui-icon name="location" />Gunakan lokasi saya</button>
                <p class="field-help location-manual-help">Browser akan meminta izin lokasi. Jika Anda tidak berada di tempat kerusakan, isi nama jalan, kelurahan, kecamatan, dan koordinat secara manual.</p>
                <p class="field-help location-map-help" id="location-map-help">Geser pin ke titik kerusakan, atau ketuk peta untuk memilih titik.</p>
                <div class="location-map" data-location-map data-latitude="{{ old('latitude') }}" data-longitude="{{ old('longitude') }}" role="region" aria-label="Peta untuk memilih lokasi kejadian" aria-describedby="location-map-help"></div>
                <div class="field-row coordinate-fields">
                    <div class="field"><label for="latitude">Latitude <small>(opsional)</small></label><input id="latitude" name="latitude" inputmode="decimal" value="{{ old('latitude') }}" placeholder="-6.8836"></div>
                    <div class="field"><label for="longitude">Longitude <small>(opsional)</small></label><input id="longitude" name="longitude" inputmode="decimal" value="{{ old('longitude') }}" placeholder="107.6102"></div>
                </div>
                <p class="notice notice-info compact"><x-ui-icon name="shield" /><span data-location-message aria-live="polite">Koordinat hanya diambil setelah Anda mengizinkan browser. Koordinat dipakai untuk mencari nama jalan dan wilayah melalui OpenStreetMap. Pastikan titiknya sesuai lokasi kerusakan, bukan rumah Anda, lalu periksa atau ubah hasil alamatnya.</span></p>
            </section>

            <section class="form-card" aria-labelledby="photo-title">
                <h2 id="photo-title">4. Foto bukti</h2>
                <p class="field-help">Foto kerusakan · JPG atau PNG, maksimal 5 MB.</p>
                <label class="upload-box @error('photo') is-invalid @enderror" for="photo"><x-ui-icon name="image" /><span data-photo-label>Pilih foto kerusakan</span><input class="visually-hidden" id="photo" name="photo" type="file" accept="image/jpeg,image/png" required data-photo-input @error('photo') aria-invalid="true" @enderror></label>
                <img class="photo-preview" data-photo-preview alt="Pratinjau foto kerusakan" hidden>
                @error('photo')<small class="field-error">{{ $message }}</small>@enderror
            </section>

            <label class="agreement-check"><input type="checkbox" name="agreement" value="1" @checked(old('agreement')) required><span>Saya memastikan informasi ini benar dan menyetujui penggunaan kontak untuk tindak lanjut laporan.</span></label>
            @error('agreement')<small class="field-error">{{ $message }}</small>@enderror
            <button class="button button-primary full-width submit-button" type="submit"><x-ui-icon name="send" />Kirim Pengaduan</button>
            <p class="submit-hint">Anda akan diminta memeriksa laporan sebelum mengirim. Data belum dikirim pada tahap ini.</p>

            <dialog class="review-dialog" data-review-dialog aria-labelledby="review-title">
                <div class="dialog-grip" aria-hidden="true"></div>
                <p class="eyebrow">SEBELUM MENGIRIM</p>
                <h2 id="review-title">Sudah sesuai?</h2>
                <p class="dialog-copy">Periksa kembali agar petugas dapat menindaklanjuti dengan tepat.</p>
                <div class="review-summary"><small>Pengaduan</small><strong data-review-subject></strong><small>Kategori & lokasi</small><span data-review-location></span><small>Bukti</small><span data-review-photo></span></div>
                <div class="notice notice-info compact"><x-ui-icon name="shield" /><span>Kontak hanya dapat dilihat petugas. Setelah dikirim, simpan ID untuk melacak laporan Anda.</span></div>
                <button class="button button-primary full-width" type="button" data-confirm-submit><x-ui-icon name="send" />Ya, Kirim Pengaduan</button>
                <button class="button button-secondary full-width" type="button" data-close-review>Periksa Kembali</button>
            </dialog>
        </form>
        <p class="submit-hint footer-note">FasilitasKita · Layanan resmi Kota Bandung<br>Laporan warga, ruang publik lebih terawat.</p>
    </div>
</x-layouts.public>
