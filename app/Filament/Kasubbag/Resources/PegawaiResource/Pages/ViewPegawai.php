<?php

namespace App\Filament\Kasubbag\Resources\PegawaiResource\Pages;

use App\Filament\Kasubbag\Resources\PegawaiResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Pages\Actions\EditAction;
use Filament\Pages\Actions\Action;
use App\Models\Jabatan;
use App\Models\RiwayatJabatan;
use Filament\Forms\Components\Select;
use App\Models\Opd;


class ViewPegawai extends ViewRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
{
    return [
        Action::make('editOpd')
            ->label('Edit OPD')
            ->modalHeading('Ubah OPD Pegawai')
            ->form([
                Select::make('opd_id')
                    ->label('Pilih OPD')
                    ->options(function() {
                        $parentId = auth()->user()->opd_id; // parent Kasubbag
                        return Opd::getDescendantsAndSelf($parentId)
                                  ->pluck('nama_opd','id');
                    })
                    ->required(),
            ])
            ->action(function (array $data) {
                $pegawai = $this->record;

                // Update OPD pegawai
                $pegawai->update([
                    'opd_id' => $data['opd_id'],
                ]);

                // Update OPD di riwayat jabatan terakhir
                $riwayat = $pegawai->riwayatJabatan()->latest('tmt_mulai')->first();
                if($riwayat) {
                    $riwayat->update([
                        'opd_id' => $data['opd_id'],
                    ]);
                }
            }),
    ];
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
