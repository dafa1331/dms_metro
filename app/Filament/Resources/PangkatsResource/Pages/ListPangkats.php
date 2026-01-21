<?php

namespace App\Filament\Resources\PangkatsResource\Pages;

use App\Filament\Resources\PangkatsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPangkats extends ListRecords
{
    protected static string $resource = PangkatsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
