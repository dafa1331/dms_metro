<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\RiwayatJabatan;

class DetailPegawaiOpd extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'detail-pegawai-opd/{record}';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.detail-pegawai-opd';

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RiwayatJabatan::query()
                    ->with(['pegawai', 'jabatan'])
                    ->where('opd_id', $this->record)
                    ->where('status_aktif', 1)
            )
            ->columns([
                Tables\Columns\TextColumn::make('pegawai.nip')
                    ->label('NIP'),

                Tables\Columns\TextColumn::make('pegawai.nama_lengkap')
                    ->label('Nama'),

                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')
                    ->label('Jabatan'),
            ]);
    }
}