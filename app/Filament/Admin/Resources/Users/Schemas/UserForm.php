<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi akun')->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')->label('Nama')->required()->maxLength(255),
                        TextInput::make('email')->label('Email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),
                        TextInput::make('instansi')->label('Instansi')->maxLength(255),
                        TextInput::make('phone_number')->label('Nomor HP')->tel()->maxLength(30),
                        Select::make('role')->label('Peran')->options(fn (): array => auth()->user()?->role === UserRole::Admin
                            ? collect(UserRole::cases())->mapWithKeys(fn (UserRole $role): array => [$role->value => $role->label()])->all()
                            : [UserRole::Petugas->value => UserRole::Petugas->label()])
                            ->default(UserRole::Petugas->value)->required(),
                        Toggle::make('is_active')->label('Akun aktif')->default(true),
                        TextInput::make('password')->label('Kata sandi')->password()->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->confirmed()->dehydrated(fn (?string $state): bool => filled($state))
                            ->minLength(12)->maxLength(255),
                        TextInput::make('password_confirmation')->label('Ulangi kata sandi')->password()->revealable()
                            ->dehydrated(false)->required(fn (string $operation): bool => $operation === 'create'),
                    ]),
                ]),
            ]);
    }
}
