<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Opd;

class RekapPegawaiOpd extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static string $view = 'filament.pages.rekap-pegawai-opd';
    protected static ?string $title = 'Struktur OPD';

    public function getOpdTree()
    {
        return Opd::whereNull('parent_id')
            ->where('aktif', 1)
            ->with([
                'children.children.children.children', // recursive eager load
                'riwayatJabatan' => function ($q) {
                    $q->where('status_aktif', 1);
                },
                'riwayatJabatan.jabatan'
            ])
            ->get();
    }
}