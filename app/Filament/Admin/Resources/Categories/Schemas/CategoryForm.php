<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi kategori')->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')->label('Nama kategori')->required()->maxLength(100)->unique(ignoreRecord: true),
                        TextInput::make('slug')->label('Slug')->required()->maxLength(120)->unique(ignoreRecord: true),
                    ]),
                    Textarea::make('description')->label('Deskripsi')->rows(3)->maxLength(5000),
                    Toggle::make('is_active')->label('Kategori aktif')->default(true),
                ]),
            ]);
    }
}
