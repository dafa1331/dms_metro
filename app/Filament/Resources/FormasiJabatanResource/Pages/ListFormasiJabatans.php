<?php

namespace App\Filament\Resources\FormasiJabatanResource\Pages;

use App\Filament\Resources\FormasiJabatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFormasiJabatans extends ListRecords
{
    protected static string $resource = FormasiJabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
