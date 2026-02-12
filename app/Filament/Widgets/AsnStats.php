<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Pegawai;

class AsnStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total ASN', Pegawai::count()),
            Stat::make('PNS', Pegawai::where('status_pegawai', 'PNS')->count()),
            Stat::make('PPPK', Pegawai::where('status_pegawai', 'PPPK')->count()),
            Stat::make('PPPK Paruh Waktu', Pegawai::where('status_pegawai', 'PPPKPW')->count()),
        ];
    }
}
