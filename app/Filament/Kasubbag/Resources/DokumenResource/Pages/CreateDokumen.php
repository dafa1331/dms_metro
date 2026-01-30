<?php

namespace App\Filament\Kasubbag\Resources\DokumenResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Kasubbag\Resources\DokumenResource;
use Filament\Facades\Filament;

class CreateDokumen extends CreateRecord
{
    protected static string $resource = DokumenResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(
            Filament::auth()->user()?->hasRole('kasubbag_kepegawaian'),
            403
        );
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_at'] = now();
        return $data;
    }
}
