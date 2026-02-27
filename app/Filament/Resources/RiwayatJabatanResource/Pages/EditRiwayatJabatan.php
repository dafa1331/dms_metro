<?php

namespace App\Filament\Resources\RiwayatJabatanResource\Pages;

use App\Filament\Resources\RiwayatJabatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatJabatan extends EditRecord
{
    protected static string $resource = RiwayatJabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
