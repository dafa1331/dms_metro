<?php

namespace App\Filament\Resources\OpdResource\Pages;

use App\Filament\Resources\OpdResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Opd; // WAJIB ADA

class CreateOpd extends CreateRecord
{
    protected static string $resource = OpdResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['parent_id'])) {
            $data['level'] = 1;
        } else {
            $parent = Opd::find($data['parent_id']);
            $data['level'] = $parent ? $parent->level + 1 : 1;
        }

        return $data;
    }
}
