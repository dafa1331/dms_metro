<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // $opd = \App\Models\Opd::where('kode_opd', $data['kode_opd'])->firstOrFail();

        // unset($data['kode_opd']); // ⛔ PENTING

        return array_merge($data, [
            'opd_id'         => $opd->id,
            'uploaded_by'    => auth()->id(),
            'uploaded_at'    => now(),
            'catatan'        => 'Dokumen valid',
            'status_dokumen' => 'terima',
            'tanggal_verif'  => now(),
        ]);
    }
}
