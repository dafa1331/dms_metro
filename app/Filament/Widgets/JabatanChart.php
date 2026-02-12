<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\RiwayatJabatan;

class JabatanChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Jenis Jabatan ASN';

    protected function getData(): array
{
    $data = \DB::table('riwayat_jabatan')
        ->join('jabatans', 'riwayat_jabatan.jabatan_id', '=', 'jabatans.id')
        ->where('riwayat_jabatan.status_aktif', 1)
        ->whereNull('riwayat_jabatan.tmt_selesai')
        ->selectRaw('jabatans.jenis_jabatan, COUNT(*) as total')
        ->groupBy('jabatans.jenis_jabatan')
        ->pluck('total', 'jabatans.jenis_jabatan')
        ->toArray();

    return [
        'datasets' => [
            [
                'data' => array_values($data),
                'backgroundColor' => [
                    '#3B82F6', // Fungsional
                    '#EF4444', // Struktural
                    '#10B981', // Pelaksana
                ],
            ],
        ],
        'labels' => array_keys($data),
    ];
}

    protected function getType(): string
    {
        return 'pie'; // bisa diganti 'bar'
    }
}
