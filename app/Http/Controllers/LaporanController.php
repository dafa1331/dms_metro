<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opd;
use App\Models\Pegawai;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function exportPdf($opdId)
    {
        $opd = Opd::findOrFail($opdId);

        $pegawai = \App\Models\Pegawai::whereHas('riwayatJabatan', function ($q) use ($opdId) {
            $q->where('opd_id', $opdId)
            ->where('status_aktif', 1);
        })->with('dokumen')->get();

        $pdf = Pdf::loadView('laporan.dokumen-opd-pdf', compact('opd', 'pegawai'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('Laporan_Dokumen_'.$opd->nama_opd.'.pdf');
    }
}
