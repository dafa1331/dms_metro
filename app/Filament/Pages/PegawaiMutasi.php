<?php

namespace App\Filament\Pages;

use App\Models\Pegawai;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;

class PegawaiMutasi extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Pegawai Mutasi';

    protected static ?string $navigationGroup = 'Data Kepegawaian';

    protected static string $view = 'filament.pages.pegawai-mutasi';

    protected function getTableQuery(): Builder
    {
        return Pegawai::query()
            ->whereHas('statusAktif', function ($q) {
                $q->where('status', 'MUTASI_KELUAR');
            });
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nip')
                ->searchable(),

            Tables\Columns\TextColumn::make('nama_lengkap')
                ->searchable(),

            Tables\Columns\TextColumn::make('statusAktif.status_kepegawaian')
                ->label('Status'),

            Tables\Columns\TextColumn::make('statusAktif.tmt_mulai')
                ->label('TMT Mutasi')
                ->date(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('detail')
                ->url(fn ($record) => route('filament.admin.resources.pegawais.view', $record))
                ->icon('heroicon-o-eye'),
        ];
    }
}
