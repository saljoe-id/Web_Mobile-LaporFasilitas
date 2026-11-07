<?php

namespace App\Filament\Admin\Resources\Complaints;

use App\Filament\Admin\Resources\Complaints\Pages\ListComplaints;
use App\Filament\Admin\Resources\Complaints\Pages\ViewComplaint;
use App\Filament\Admin\Resources\Complaints\RelationManagers\StatusHistoriesRelationManager;
use App\Filament\Admin\Resources\Complaints\Schemas\ComplaintInfolist;
use App\Filament\Admin\Resources\Complaints\Tables\ComplaintsTable;
use App\Models\Complaint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Pengaduan';

    protected static ?string $modelLabel = 'pengaduan';

    protected static ?string $pluralModelLabel = 'Pengaduan';

    protected static string|UnitEnum|null $navigationGroup = 'Operasional';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['category', 'activeAssignment.user']);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ComplaintInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComplaintsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StatusHistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComplaints::route('/'),
            'view' => ViewComplaint::route('/{record}'),
        ];
    }
}
