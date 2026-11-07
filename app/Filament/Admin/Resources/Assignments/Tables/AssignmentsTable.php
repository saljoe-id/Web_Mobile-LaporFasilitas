<?php

namespace App\Filament\Admin\Resources\Assignments\Tables;

use App\Enums\AssignmentStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('complaint.complaint_code')->label('Nomor pengaduan')->searchable()->sortable(),
                TextColumn::make('complaint.title')->label('Pengaduan')->searchable()->limit(40),
                TextColumn::make('user.name')->label('Petugas')->searchable()->sortable(),
                TextColumn::make('assignedBy.name')->label('Ditugaskan oleh')->placeholder('—'),
                TextColumn::make('assigned_at')->label('Waktu penugasan')->dateTime('d M Y, H:i')->sortable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (AssignmentStatus $state): string => $state->label())
                    ->color(fn (AssignmentStatus $state): string => match ($state) {
                        AssignmentStatus::Ditugaskan => 'warning',
                        AssignmentStatus::Diproses => 'info',
                        AssignmentStatus::Selesai => 'success',
                        AssignmentStatus::Ditolak, AssignmentStatus::Dibatalkan => 'gray',
                    }),
                TextColumn::make('notes')->label('Catatan')->wrap()->limit(80),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(collect(AssignmentStatus::cases())->mapWithKeys(fn (AssignmentStatus $status): array => [$status->value => $status->label()])->all()),
                SelectFilter::make('user')->label('Petugas')->relationship('user', 'name'),
            ])
            ->recordActions([]);
    }
}
