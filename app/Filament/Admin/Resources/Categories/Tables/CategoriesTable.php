<?php

namespace App\Filament\Admin\Resources\Categories\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama kategori')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                TextColumn::make('description')->label('Deskripsi')->limit(60)->wrap(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('complaints_count')->label('Jumlah laporan')->counts('complaints')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Status kategori')->trueLabel('Aktif')->falseLabel('Nonaktif'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
