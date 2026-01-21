<?php

namespace App\Exports;

use App\Models\Opd;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OpdExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Opd::orderBy('parent_id')->orderBy('urutan')->get([
            'kode_opd',
            'nama_opd',
            'jenis_opd',
            'parent_id',
            'level',
            'urutan',
            'aktif'
        ]);
    }

    public function headings(): array
    {
        return [
            'Kode OPD',
            'Nama OPD',
            'Jenis OPD',
            'Parent ID',
            'Level',
            'Urutan',
            'Aktif'
        ];
    }
}

