<?php

namespace App\Filament\Resources\RefAgamaResource\Pages;

use App\Filament\Resources\RefAgamaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRefAgama extends EditRecord
{
    protected static string $resource = RefAgamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
