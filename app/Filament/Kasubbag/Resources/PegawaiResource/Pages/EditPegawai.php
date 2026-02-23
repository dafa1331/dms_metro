<?php

namespace App\Filament\Kasubbag\Resources\PegawaiResource\Pages;

use App\Filament\Kasubbag\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Components\Select;

use App\Models\RiwayatJabatan;
use App\Models\Jabatan;

class EditPegawai extends EditRecord
{
    protected static string $resource = PegawaiResource::class;

    // Header Actions
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(), // tombol hapus tetap ada
        ];
    }

    // Form Schema
    protected function getFormSchema(): array
    {
        return [
            Select::make('jabatan_aktif_id')
                ->label('Jabatan Aktif')
                ->options(Jabatan::pluck('nama_jabatan', 'id'))
                ->required(),
        ];
    }

    // Hook setelah record disimpan
    protected function afterSave(): void
    {
        $pegawai = $this->record;

        // Simpan ke riwayat jabatan
        RiwayatJabatan::create([
            'pegawai_id' => $pegawai->id,
            'jabatan_id' => $pegawai->jabatan_aktif_id,
            'tmt' => now(), // bisa diganti dengan tanggal mulai jabatan
            'keterangan' => 'Perubahan jabatan otomatis via edit pegawai',
        ]);
    }
}