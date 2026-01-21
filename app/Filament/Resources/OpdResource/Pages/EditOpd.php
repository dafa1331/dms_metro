<?php

namespace App\Filament\Resources\OpdResource\Pages;

use App\Filament\Resources\OpdResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Opd;

class EditOpd extends EditRecord
{
    protected static string $resource = OpdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
