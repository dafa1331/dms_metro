<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\AsnStats;
use App\Filament\Widgets\JabatanChart;
use App\Filament\Widgets\UsiaChart;
use App\Filament\Widgets\AktivitasTerbaru;
use App\Filament\Widgets\ProgressDokumenPerOpd;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderWidgets(): array
    {
        return [
            AsnStats::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            JabatanChart::class,
            UsiaChart::class,
            ProgressDokumenPerOpd::class,
            // AktivitasTerbaru::class,
        ];
    }
    
}
