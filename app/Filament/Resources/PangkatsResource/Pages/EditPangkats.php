<?php

namespace App\Filament\Resources\PangkatsResource\Pages;

use App\Filament\Resources\PangkatsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPangkats extends EditRecord
{
    protected static string $resource = PangkatsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
