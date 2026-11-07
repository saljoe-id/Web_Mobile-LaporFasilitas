<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Enums\UserRole;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('instansi')->label('Instansi')->searchable()->toggleable(),
                TextColumn::make('phone_number')->label('Nomor HP')->searchable()->toggleable(),
                TextColumn::make('role')->label('Peran')->badge()
                    ->formatStateUsing(fn (UserRole $state): string => $state->label())
                    ->color(fn (UserRole $state): string => $state === UserRole::Admin ? 'danger' : 'info'),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->filters([
                SelectFilter::make('role')->label('Peran')->options(collect(UserRole::cases())->mapWithKeys(fn (UserRole $role): array => [$role->value => $role->label()])->all()),
                TernaryFilter::make('is_active')->label('Status akun')->trueLabel('Aktif')->falseLabel('Nonaktif'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
