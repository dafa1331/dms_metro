<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     if (isset($data['kode_opd'])) {
    //         $opd = \App\Models\Opd::where('kode_opd', $data['kode_opd'])->firstOrFail();
    //         $data['opd_id'] = $opd->id;
    //         unset($data['kode_opd']);
    //     }

    //     return $data;
    // }

}
