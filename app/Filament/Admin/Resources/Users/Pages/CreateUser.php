<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Enums\UserRole;
use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()?->role !== UserRole::Admin) {
            $data['role'] = UserRole::Petugas;
        }

        return $data;
    }
}
