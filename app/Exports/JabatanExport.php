<?php

namespace App\Exports;

use App\Models\Jabatan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JabatanExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Jabatan::select(
            'kode_jabatan',
            'nama_jabatan',
            'jenis_jabatan',
            'jenjang_jabatan_id',
            'jenis_fungsional',
            'eselon',
            'kelas_jabatan',
            'aktif'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Kode Jabatan',
            'Nama Jabatan',
            'Jenis Jabatan',
            'jenjang_jabatan_id',
            'jenis_fungsional',
            'Eselon',
            'Kelas Jabatan',
            'Aktif',
        ];
    }
}
