<?php

namespace App\Filament\Admin\Resources\Complaints\Schemas;

use App\Enums\ComplaintStatus;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ComplaintInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi pengaduan')->columns(2)->schema([
                    TextEntry::make('complaint_code')->label('Nomor pengaduan'),
                    TextEntry::make('status')->label('Status')->badge()
                        ->formatStateUsing(fn (ComplaintStatus $state): string => $state->label())
                        ->color(fn (ComplaintStatus $state): string => match ($state) {
                            ComplaintStatus::Pending, ComplaintStatus::Baru => 'warning',
                            ComplaintStatus::Diverifikasi, ComplaintStatus::Diproses => 'info',
                            ComplaintStatus::Selesai => 'success',
                            ComplaintStatus::Ditolak => 'danger',
                        }),
                    TextEntry::make('category.name')->label('Kategori'),
                    TextEntry::make('created_at')->label('Waktu laporan')->dateTime('d M Y, H:i'),
                    TextEntry::make('title')->label('Judul')->columnSpanFull(),
                    TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
                    TextEntry::make('address')->label('Alamat kejadian'),
                    TextEntry::make('village')->label('Kelurahan'),
                    TextEntry::make('district')->label('Kecamatan'),
                    TextEntry::make('latitude')->label('Latitude'),
                    TextEntry::make('longitude')->label('Longitude'),
                    ImageEntry::make('photo_path')->label('Foto bukti')->disk('public')->columnSpanFull(),
                ]),
                Section::make('Data pelapor (rahasia petugas)')->columns(2)->schema([
                    TextEntry::make('reporter_name')->label('Nama pelapor'),
                    TextEntry::make('reporter_phone')->label('Nomor HP'),
                ]),
                Section::make('Penugasan aktif')->columns(2)->schema([
                    TextEntry::make('activeAssignment.user.name')->label('Petugas'),
                    TextEntry::make('activeAssignment.assigned_at')->label('Ditugaskan')->dateTime('d M Y, H:i'),
                    TextEntry::make('activeAssignment.notes')->label('Catatan penugasan')->columnSpanFull(),
                ])->collapsible(),
            ]);
    }
}
