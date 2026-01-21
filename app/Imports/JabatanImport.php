<?php

namespace App\Imports;

use App\Models\Jabatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JabatanImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return Jabatan::updateOrCreate(
            ['kode_jabatan' => $row['kode_jabatan']],
            [
                'nama_jabatan' => $row['nama_jabatan'],
                'jenis_jabatan' => $row['jenis_jabatan'],
                'jenjang_jabatan_id' => $row['jenjang_jabatan_id'],
                'jenis_fungsional' => $row['jenis_fungsional'],
                'eselon' => $row['eselon'] ?? null,
                'kelas_jabatan' => $row['kelas_jabatan'] ?? null,
                'aktif' => $row['aktif'] ?? 1,
            ]
        );
    }
}
