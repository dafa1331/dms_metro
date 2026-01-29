<?php

namespace App\Filament\Kasubbag\Resources\DocumentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Kasubbag\Resources\DocumentResource;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_at'] = now();
        return $data;
    }
}
