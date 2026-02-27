<?php

namespace App\Filament\Resources\RiwayatKepegawaianResource\Pages;

use App\Filament\Resources\RiwayatKepegawaianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatKepegawaian extends EditRecord
{
    protected static string $resource = RiwayatKepegawaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
