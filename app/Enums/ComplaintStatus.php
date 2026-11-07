<?php

namespace App\Enums;

enum ComplaintStatus: string
{
    case Pending = 'pending';
    case Baru = 'baru';
    case Diverifikasi = 'diverifikasi';
    case Diproses = 'diproses';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::Baru => 'Baru',
            self::Diverifikasi => 'Diverifikasi',
            self::Diproses => 'Diproses',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
        };
    }

    /**
     * @return list<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending, self::Baru => [self::Diverifikasi, self::Ditolak],
            self::Diverifikasi => [self::Diproses, self::Ditolak],
            self::Diproses => [self::Selesai, self::Ditolak],
            self::Selesai, self::Ditolak => [],
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->allowedTransitions(), true);
    }
}
