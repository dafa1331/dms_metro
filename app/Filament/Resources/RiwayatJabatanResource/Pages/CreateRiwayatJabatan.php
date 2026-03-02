<?php

namespace App\Filament\Resources\RiwayatJabatanResource\Pages;

use App\Filament\Resources\RiwayatJabatanResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\RiwayatJabatan;
use App\Models\Jabatan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateRiwayatJabatan extends CreateRecord
{
    protected static string $resource = RiwayatJabatanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $tmtMulai   = Carbon::parse($data['tmt_mulai']);
            $tmtSebelum = $tmtMulai->copy()->subDay();

            $pegawaiId  = $data['pegawai_id'];
            $jabatan    = Jabatan::find($data['jabatan_id']);

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ NONAKTIFKAN SEMUA JABATAN AKTIF PEGAWAI INI
            |--------------------------------------------------------------------------
            */

            RiwayatJabatan::where('pegawai_id', $pegawaiId)
                ->where('status_aktif', 1)
                ->update([
                    'status_aktif' => 0,
                    'tmt_selesai'  => $tmtSebelum,
                ]);

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ JIKA JABATAN BARU ADALAH STRUKTURAL
            |--------------------------------------------------------------------------
            */

            if ($jabatan?->jenis_jabatan === 'struktural') {

                // Nonaktifkan pejabat lama di jabatan tersebut
                RiwayatJabatan::where('jabatan_id', $data['jabatan_id'])
                    ->where('status_aktif', 1)
                    ->update([
                        'status_aktif' => 0,
                        'tmt_selesai'  => $tmtSebelum,
                    ]);

                $data['is_struktural'] = 1;

            } else {
                $data['is_struktural'] = 0;
            }

            $data['status_aktif'] = 1;

            return $data;
        });
    }
}