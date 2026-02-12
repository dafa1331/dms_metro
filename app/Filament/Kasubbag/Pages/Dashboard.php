<?php

namespace App\Filament\Kasubbag\Pages;
use App\Filament\Widgets\ProgressUploadKasubbagAsnStats;
use App\Filament\Widgets\StatistikKasubbag;


use Filament\Pages\Page;

class Dashboard extends Page
{

    protected static string $view = 'filament.kasubbag.pages.dashboard';

    protected static ?string $title = 'Dashboard DMS BKPSDM';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderWidgets(): array
    {
        return [
            StatistikKasubbag::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            ProgressUploadKasubbagAsnStats::class,
        ];
    }
    

}
