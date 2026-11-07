<?php

namespace App\Enums;

enum AssignmentStatus: string
{
    case Ditugaskan = 'ditugaskan';
    case Diproses = 'diproses';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Ditugaskan => 'Ditugaskan',
            self::Diproses => 'Sedang dikerjakan',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
            self::Dibatalkan => 'Dibatalkan',
        };
    }
}
