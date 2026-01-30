<?php

namespace App\Filament\Kasubbag\Resources\DokumenResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use App\Filament\Kasubbag\Resources\DokumenResource;
use Filament\Facades\Filament;

class ListDokumens extends ListRecords
{
    protected static string $resource = DokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () =>
                    Filament::auth()->user()?->hasRole('kasubbag_kepegawaian')
                ),
        ];
    }
}
