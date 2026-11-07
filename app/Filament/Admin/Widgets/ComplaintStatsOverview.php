<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ComplaintStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total pengaduan', Complaint::query()->count())
                ->description('Seluruh laporan warga')
                ->color('primary'),
            Stat::make('Menunggu verifikasi', Complaint::query()
                ->whereIn('status', [ComplaintStatus::Pending->value, ComplaintStatus::Baru->value])->count())
                ->description('Perlu diperiksa petugas')
                ->color('warning'),
            Stat::make('Sedang ditangani', Complaint::query()
                ->whereIn('status', [ComplaintStatus::Diverifikasi->value, ComplaintStatus::Diproses->value])->count())
                ->description('Sudah diverifikasi atau diproses')
                ->color('info'),
            Stat::make('Selesai', Complaint::query()->where('status', ComplaintStatus::Selesai->value)->count())
                ->description('Pengaduan yang dituntaskan')
                ->color('success'),
        ];
    }
}
