<?php

namespace App\Filament\Admin\Resources\Complaints\RelationManagers;

use App\Enums\ComplaintStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'statusHistories';

    protected static ?string $title = 'Riwayat status';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y, H:i')->sortable(),
                TextColumn::make('old_status')->label('Status sebelumnya')->formatStateUsing(
                    fn (?ComplaintStatus $state): string => $state?->label() ?? 'Pengaduan dibuat',
                ),
                TextColumn::make('new_status')->label('Status baru')->badge()->formatStateUsing(
                    fn (ComplaintStatus $state): string => $state->label(),
                ),
                TextColumn::make('user.name')->label('Diubah oleh')->placeholder('Warga'),
                TextColumn::make('notes')->label('Catatan')->wrap()->limit(100),
            ])
            ->defaultSort('created_at');
    }
}
