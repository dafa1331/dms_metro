<?php

namespace App\Http\Controllers;

use App\Exports\RekonPegawaiExport;
use Maatwebsite\Excel\Facades\Excel;

class RekonPegawaiExportController extends Controller
{
    public function export()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $fileName = 'rekon-pegawai-' . now()->format('YmdHis') . '.csv';

        return Excel::download(
            new RekonPegawaiExport,
            $fileName
        );
    }
}