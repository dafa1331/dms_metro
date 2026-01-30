<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Pegawai;

class PegawaiStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pegawai', Pegawai::count()),
            Stat::make('PNS', Pegawai::where('jenis_pegawai', 'PNS')->count()),
            Stat::make('PPPK', Pegawai::where('jenis_pegawai', 'PPPK')->count()),
            Stat::make('PPPK Paruh Waktu', Pegawai::where('jenis_pegawai', 'PPPKPW')->count()),
        ];
    }
}
