<?php

namespace App\Filament\Kasubbag\Resources\DokumenResource\Pages;

use App\Filament\Kasubbag\Resources\DokumenResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Document;

class CreateDokumen extends CreateRecord
{
    protected static string $resource = DokumenResource::class;

   # =========================================
    # mutateFormDataBeforeCreate (untuk insert baru)
    # =========================================
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $existing = Document::where('nip', $data['nip'])
            ->where('type', $data['type'])
            ->first();

        if ($existing && $existing->status_dokumen === 'tolak') {
            $file = $data['temp_path'];

                $path = $file->store('temp/dokumen/'.$get('type').'/' . $data['type'], 'local');
                $originalName = $file->getClientOriginalName();
                $mime = $file->getMimeType();
                $size = $file->getSize();

            // if ($file instanceof TemporaryUploadedFile) {
            //     // Simpan file perbaikan di folder temporary
            //     $path = $file->store('temp/dokumen/' . $data['type'], 'local');
            //     $originalName = $file->getClientOriginalName();
            //     $mime = $file->getMimeType();
            //     $size = $file->getSize();
            // } else {
            //     // Pakai data lama jika bukan upload baru
            //     $path = $existing->temp_path;
            //     $originalName = $existing->original_name;
            //     $mime = $existing->mime;
            //     $size = $existing->size;
            // }

            $existing->update([
                'temp_path' => $path,
                'original_name' => $originalName,
                'mime' => $mime,
                'size' => $size,
                'status_dokumen' => 'proses',
                'catatan' => $data['catatan'] ?? null,
                'uploaded_at' => now(),
            ]);

            // Hentikan insert baru
            throw new \Filament\Support\Exceptions\Halt();
        }

        // Dokumen baru
        $data['status_dokumen'] = 'proses';
        $data['opd_id'] = auth()->user()->opd_id;
        $data['uploaded_at'] = now();

        return $data;
    }
}



