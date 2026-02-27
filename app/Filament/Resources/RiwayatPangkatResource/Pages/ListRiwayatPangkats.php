<?php

namespace App\Filament\Resources\RiwayatPangkatResource\Pages;

use App\Filament\Resources\RiwayatPangkatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatPangkats extends ListRecords
{
    protected static string $resource = RiwayatPangkatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
