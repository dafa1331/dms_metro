<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PegawaiStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Pegawai', 100),
        ];
    }
}