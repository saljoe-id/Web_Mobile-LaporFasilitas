<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Enums\UserRole;
use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (auth()->user()?->role !== UserRole::Admin) {
            $data['role'] = UserRole::Petugas;
        }

        return $data;
    }
}
