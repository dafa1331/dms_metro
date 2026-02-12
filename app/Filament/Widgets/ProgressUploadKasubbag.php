<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Pegawai;
use App\Models\Document;

class ProgressUploadKasubbag extends Widget
{
    protected static string $view = 'filament.kasubbag.widgets.progress-upload-kasubbag';

    protected int|string|array $columnSpan = 'full';

    public function getData(): array
    {
        $opdId = auth()->user()->opd_id;

        $jumlahPegawai = Pegawai::whereHas('riwayatJabatan', function ($q) use ($opdId) {
            $q->where('status_aktif', 1)
              ->where('opd_id', $opdId);
        })->count();

        $dokumen = Document::whereHas('pegawai.riwayatJabatan', function ($q) use ($opdId) {
            $q->where('status_aktif', 1)
              ->where('opd_id', $opdId);
        })->count();

        $target = $jumlahPegawai * 5;

        $percent = $target > 0 ? round(($dokumen / $target) * 100, 2) : 0;

        return [
            'percent' => $percent,
        ];
    }
}
