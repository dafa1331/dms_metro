<?php

namespace App\Filament\Pages;

use App\Models\Pegawai;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Kasubbag\Resources\PegawaiResource\Pages;

class PegawaiPensiun extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Pegawai Pensiun / BUP';

    protected static ?string $navigationGroup = 'Data Kepegawaian';

    protected static string $view = 'filament.pages.pegawai-pensiun';

    protected function getTableQuery(): Builder
    {
        return Pegawai::query()
            ->whereHas('statusAktif', function ($q) {
                $q->where('status', 'PENSIUN');
            });
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nip')
                ->searchable(),

            Tables\Columns\TextColumn::make('nama_lengkap')
                ->searchable(),

            Tables\Columns\TextColumn::make('statusAktif.tmt_status')
                ->label('TMT Pensiun')
                ->date(),

            Tables\Columns\TextColumn::make('tanggal_lahir')
                ->label('Tanggal Lahir')
                ->date(),

            Tables\Columns\TextColumn::make('tanggal_lahir')
                ->label('Usia Saat Ini')
                ->formatStateUsing(fn ($record) =>
                    \Carbon\Carbon::parse($record->tanggal_lahir)->age . ' Tahun'
                ),
        ];
    }

   protected function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make(),
        ];
    }

     public static function getPages(): array
    {
        return [
            'view' => Pages\ViewPegawai::route('/{record}'), // <- pastikan ini ada
        ];
    }
}
