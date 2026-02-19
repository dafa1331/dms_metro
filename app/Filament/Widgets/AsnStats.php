<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Pegawai;

class AsnStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $pegawaiAktif = Pegawai::whereHas('statusAktif', function ($q) {
            $q->where('status', 'AKTIF');
        });

        return [
            Stat::make('Total ASN Aktif', (clone $pegawaiAktif)->count()),

            Stat::make('PNS Aktif',
                (clone $pegawaiAktif)->where('status_pegawai', 'PNS')->count()
            ),

            Stat::make('PPPK Aktif',
                (clone $pegawaiAktif)->where('status_pegawai', 'PPPK')->count()
            ),

            Stat::make('PPPK Paruh Waktu Aktif',
                (clone $pegawaiAktif)->where('status_pegawai', 'PPPKPW')->count()
            ),
        ];
    }
}
