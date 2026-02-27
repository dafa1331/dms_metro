<?php

namespace App\Imports;

use App\Models\RiwayatPangkat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RiwayatPangkatImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            RiwayatPangkat::create([
                'pegawai_id'     => $row['pegawai_id'],
                'ref_id_pangkat' => $row['ref_id_pangkat'],
                'tmt_pangkat'    => $row['tmt_pangkat'],
                'nomor_sk'       => $row['nomor_sk'],
                'tanggal_sk'     => $row['tanggal_sk'],
                'keterangan'     => $row['keterangan'],
                'is_aktif'       => $row['is_aktif'] ?? 0,
            ]);
        }
    }
}