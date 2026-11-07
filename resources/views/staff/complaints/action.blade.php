@php
    $actionTitles = [
        'verify' => 'Verifikasi laporan',
        'reject' => 'Tolak pengaduan',
        'assign' => 'Tugaskan petugas',
        'progress' => 'Mulai & perbarui proses',
        'complete' => 'Tandai selesai',
    ];
    $actionButtons = [
        'verify' => 'Verifikasi Pengaduan',
        'reject' => 'Konfirmasi Penolakan',
        'assign' => 'Simpan Penugasan',
        'progress' => 'Simpan Pembaruan',
        'complete' => 'Simpan & Tandai Selesai',
    ];
@endphp

<x-layouts.staff :title="$actionTitles[$action]" active="Pengaduan">
    <a class="back-link" href="{{ route('staff.complaints.show', $complaint) }}">← Detail pengaduan</a>
    <div class="staff-page-heading"><div><p class="eyebrow page-eyebrow">TINDAKAN PETUGAS · {{ $complaint->complaint_code }}</p><h1>{{ $actionTitles[$action] }}</h1><p>{{ $complaint->title }} · {{ $complaint->category->name }}</p></div><span class="status-badge status-{{ $complaint->status->value }}">{{ $complaint->status->label() }}</span></div>

    <section class="staff-action-card">
        @if ($action === 'verify')
            <div class="notice notice-info"><x-ui-icon name="shield" /><span>Periksa kategori, lokasi, dan foto sebelum laporan diteruskan untuk penanganan.</span></div>
        @elseif ($action === 'reject')
            <div class="notice notice-error"><x-ui-icon name="error" /><span>Penolakan akan mengakhiri penanganan laporan. Alasan ini dapat dilihat warga.</span></div>
        @elseif ($action === 'assign')
            <div class="notice notice-info"><x-ui-icon name="building" /><span>Pilih petugas yang bertanggung jawab menindaklanjuti laporan. Catatan penugasan hanya untuk internal.</span></div>
        @elseif ($action === 'progress')
            <div class="notice notice-info"><x-ui-icon name="clock" /><span>Catatan progres ini akan ditampilkan pada riwayat publik laporan.</span></div>
        @else
            <div class="notice notice-info"><x-ui-icon name="check" /><span>Pastikan perbaikan sudah dilakukan. Catatan hasil akan terlihat oleh warga.</span></div>
        @endif

        <form class="staff-action-form" action="{{ route('staff.complaints.action.store', [$complaint, $action]) }}" method="POST">
            @csrf
            @if ($action === 'assign')
                <div class="field"><label for="user_id">Petugas lapangan <span>*</span></label><select id="user_id" name="user_id" required><option value="">Pilih petugas</option>@foreach($staffUsers as $staffUser)<option value="{{ $staffUser->id }}" @selected(old('user_id') == $staffUser->id)>{{ $staffUser->name }}@if($staffUser->instansi) · {{ $staffUser->instansi }}@endif</option>@endforeach</select>@error('user_id')<small class="field-error">{{ $message }}</small>@enderror</div>
            @endif
            @if ($action !== 'verify' && $action !== 'assign')
                <div class="field"><label for="notes">{{ $action === 'reject' ? 'Alasan penolakan' : ($action === 'complete' ? 'Catatan hasil perbaikan' : 'Catatan progres') }} <span>*</span></label><textarea id="notes" name="notes" rows="5" maxlength="2000" required>{{ old('notes') }}</textarea>@error('notes')<small class="field-error">{{ $message }}</small>@enderror</div>
            @elseif ($action === 'assign')
                <div class="field"><label for="notes">Catatan penugasan <small>(opsional)</small></label><textarea id="notes" name="notes" rows="4" maxlength="2000">{{ old('notes') }}</textarea>@error('notes')<small class="field-error">{{ $message }}</small>@enderror</div>
            @else
                <div class="field"><label for="notes">Catatan verifikasi <small>(opsional, ditampilkan kepada warga)</small></label><textarea id="notes" name="notes" rows="4" maxlength="2000">{{ old('notes') }}</textarea>@error('notes')<small class="field-error">{{ $message }}</small>@enderror</div>
            @endif
            <p class="field-help">ID laporan: {{ $complaint->complaint_code }} · Perubahan status tercatat otomatis.</p>
            <button class="button {{ $action === 'reject' ? 'button-danger' : 'button-primary' }} full-width" type="submit">{{ $actionButtons[$action] }}</button>
            <a class="button button-secondary full-width" href="{{ route('staff.complaints.show', $complaint) }}">Batal</a>
        </form>
    </section>
</x-layouts.staff>
