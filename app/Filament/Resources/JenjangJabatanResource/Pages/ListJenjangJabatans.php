<?php

namespace App\Filament\Resources\JenjangJabatanResource\Pages;

use App\Filament\Resources\JenjangJabatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenjangJabatans extends ListRecords
{
    protected static string $resource = JenjangJabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
