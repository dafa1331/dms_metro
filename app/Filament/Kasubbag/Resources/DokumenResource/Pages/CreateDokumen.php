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
        // Jika bukan riwayat → hanya 1 dokumen per type
        if (($data['is_riwayat'] ?? 0) == 0) {

            $existing = Document::where('nip', $data['nip'])
                ->where('type', $data['type'])
                ->first();

            if ($existing && $existing->status_dokumen === 'tolak') {

                if ($existing->temp_path) {
                    \Storage::disk('local')->delete($existing->temp_path);
                }

                $existing->update([
                    'temp_path' => $data['temp_path'],
                    'original_name' => $data['original_name'],
                    'mime' => $data['mime'],
                    'size' => $data['size'],
                    'status_dokumen' => 'proses',
                    'catatan' => null,
                    'uploaded_at' => now(),
                ]);

                throw new \Filament\Support\Exceptions\Halt();
            }
        }

        // default insert baru
        $data['status_dokumen'] = 'proses';
        $data['opd_id'] = auth()->user()->opd_id;
        $data['uploaded_at'] = now();

        return $data;
    }


}



