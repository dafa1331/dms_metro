<?php

namespace App\Filament\Resources\JenjangJabatanResource\Pages;

use App\Filament\Resources\JenjangJabatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenjangJabatan extends EditRecord
{
    protected static string $resource = JenjangJabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
