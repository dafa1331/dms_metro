<?php

namespace App\Filament\Resources\RiwayatPangkatResource\Pages;

use App\Filament\Resources\RiwayatPangkatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatPangkat extends EditRecord
{
    protected static string $resource = RiwayatPangkatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
