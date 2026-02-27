<?php

namespace App\Imports;

use App\Models\Pegawai;
use App\Models\RiwayatKepegawaian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RiwayatKepegawaianImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $pegawai = Pegawai::where('nip', $row['nip'])->first();

            if (!$pegawai) {
                continue;
            }

            // Nonaktifkan riwayat lama
            RiwayatKepegawaian::where('pegawai_id', $pegawai->id)
                ->update(['is_aktif' => 0]);

            RiwayatKepegawaian::create([
                'pegawai_id'       => $pegawai->id,
                'jenis_kepegawaian'=> $row['jenis_kepegawaian'],
                'status'           => $row['status'],
                'tmt_status'       => $row['tmt_status'],
                'tmt_selesai'      => $row['tmt_selesai'] ?? null,
                'periode_kontrak'  => $row['periode_kontrak'] ?? null,
                'nomor_sk'         => $row['nomor_sk'] ?? null,
                'tanggal_sk'       => $row['tanggal_sk'] ?? null,
                'keterangan'       => $row['keterangan'] ?? null,
                'is_aktif'         => $row['is_aktif'] ?? 1,
            ]);
        }
    }
}