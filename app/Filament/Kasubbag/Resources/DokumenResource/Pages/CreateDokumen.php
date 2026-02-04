<?php

namespace App\Filament\Kasubbag\Resources\DokumenResource\Pages;

use App\Filament\Kasubbag\Resources\DokumenResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Document;

class CreateDokumen extends CreateRecord
{
    protected static string $resource = DokumenResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $existing = Document::where('nip', $data['nip'])
            ->where('type', $data['type'])
            ->first();

        if ($existing) {
            // hapus file lama (jika masih temp)
            if ($existing->temp_path) {
                \Storage::disk('local')->delete($existing->temp_path);
            }

            $existing->update($data);

            throw new \Filament\Support\Exceptions\Halt();
        }

        $data['status_dokumen'] = 'proses';
        $data['opd_id'] = auth()->user()->opd_id;
        $data['uploaded_at'] = now();

        return $data;
    }
}
