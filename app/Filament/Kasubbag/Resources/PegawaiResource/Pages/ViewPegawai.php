<?php

namespace App\Filament\Kasubbag\Resources\PegawaiResource\Pages;

use App\Filament\Kasubbag\Resources\PegawaiResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ViewPegawai extends ViewRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return []; // ⛔ tidak ada edit/delete
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Data Pegawai')
                    ->schema([
                        TextEntry::make('nip')->label('NIP'),
                        TextEntry::make('nama_lengkap')->label('Nama'),
                        TextEntry::make('status_pegawai')->label('Status Pegawai'),
                    ])
                    ->columns(2),

                Section::make('Kepangkatan')
                    ->schema([
                        TextEntry::make('nama_pangkat')
                            ->label('Pangkat'),
                    ]),

                Section::make('Jabatan')
                    ->schema([
                        TextEntry::make('nama_jabatan_lengkap')
                            ->label('Jabatan'),

                        TextEntry::make('jabatanAktif.opd.nama_opd')
                            ->label('OPD'),

                        TextEntry::make('jabatanAktif.opd.parent.nama_opd')
                            ->label('OPD Induk'),
                    ])
                    ->columns(2),
            ]);
    }
}
