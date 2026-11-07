<?php

namespace App\Filament\Admin\Resources\Assignments;

use App\Filament\Admin\Resources\Assignments\Pages\ListAssignments;
use App\Filament\Admin\Resources\Assignments\Tables\AssignmentsTable;
use App\Models\Assignment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Penugasan';

    protected static ?string $modelLabel = 'penugasan';

    protected static ?string $pluralModelLabel = 'Penugasan';

    protected static string|UnitEnum|null $navigationGroup = 'Operasional';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return AssignmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssignments::route('/'),
        ];
    }
}
