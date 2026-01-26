<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Exports\RekonPegawaiExport;
use Maatwebsite\Excel\Facades\Excel;

class RekonPegawaiExportController extends Controller
{
    public function export()
    {
        $data = Pegawai::with([
            'jabatanAktif.opd.parent',
            'jabatanAktif.jabatan.jenjangJabatan',
            'pangkatTerakhir.pangkat',
            'kepegawaianAktif',
        ])
        ->whereHas('jabatanAktif')
        ->whereHas('pangkatTerakhir')
        ->get();

        return Excel::download(
            new RekonPegawaiExport($data),
            'rekon-pegawai.csv'
        );
    }
}
