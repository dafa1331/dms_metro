<?php

namespace App\Imports;

use App\Models\Opd;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OpdImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $parent = null;

        if (!empty($row['parent_kode'])) {
            $parent = Opd::where('kode_opd', $row['parent_kode'])->first();
        }

        return new Opd([
            'kode_opd' => $row['kode_opd'],
            'nama_opd' => $row['nama_opd'],
            'jenis_opd' => $row['jenis_opd'],
            'parent_id' => $parent?->id,
            'level' => $parent ? $parent->level + 1 : 1,
            'urutan' => $row['urutan'] ?? 0,
            'aktif' => $row['aktif'] ?? 1,
        ]);
    }
}
