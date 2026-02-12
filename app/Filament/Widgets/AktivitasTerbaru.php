<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Pegawai;

class AktivitasTerbaru extends TableWidget
{
    protected static ?string $heading = 'Aktivitas ASN Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pegawai::query()->latest()
            )
            ->columns([
                TextColumn::make('nama')->label('Nama'),
                TextColumn::make('nip')->label('NIP'),
                TextColumn::make('updated_at')
                    ->label('Update')
                    ->since(),
            ]);
    }
}
