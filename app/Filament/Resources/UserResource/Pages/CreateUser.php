<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // $this->record->syncRoles($this->data['roles']);
        $roles = Role::whereIn('id', $this->data['roles'])->get();
        $this->record->syncRoles($roles);
    }
}
