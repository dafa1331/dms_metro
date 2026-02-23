<?php

namespace App\Filament\Resources\FormasiJabatanResource\Pages;

use App\Filament\Resources\FormasiJabatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFormasiJabatan extends EditRecord
{
    protected static string $resource = FormasiJabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
