<?php

namespace App\Filament\Resources\RiwayatKepegawaianResource\Pages;

use App\Filament\Resources\RiwayatKepegawaianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatKepegawaians extends ListRecords
{
    protected static string $resource = RiwayatKepegawaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
