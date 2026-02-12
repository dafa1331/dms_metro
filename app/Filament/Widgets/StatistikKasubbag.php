<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;
use App\Models\Pegawai;
use App\Models\Document;

class StatistikKasubbag extends BaseWidget
{
    protected function getCards(): array
    {
        $opdId = auth()->user()->opd_id;

        $jumlahPegawai = Pegawai::whereHas('riwayatJabatan', function ($q) use ($opdId) {
            $q->where('status_aktif', 1)
              ->where('opd_id', $opdId);
        })->count();

        $dokumenTerupload = Document::whereHas('pegawai.riwayatJabatan', function ($q) use ($opdId) {
            $q->where('status_aktif', 1)
              ->where('opd_id', $opdId);
        })->count();

        $targetDokumen = $jumlahPegawai * 5; // misal 5 dokumen wajib

        return [
            Card::make('Jumlah Pegawai', $jumlahPegawai)
                ->color('primary'),

            Card::make('Dokumen Terupload', $dokumenTerupload)
                ->color('success'),

            Card::make('Target Dokumen', $targetDokumen)
                ->color('info'),
        ];
    }
}
