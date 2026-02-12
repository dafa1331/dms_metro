<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Pegawai;
use Carbon\Carbon;

class UsiaChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Usia ASN';

    protected function getData(): array
    {
        $pegawai = Pegawai::get();

        $usia = [
            '<30' => 0,
            '30-40' => 0,
            '41-50' => 0,
            '51+' => 0,
        ];

        foreach ($pegawai as $p) {
            $age = Carbon::parse($p->tanggal_lahir)->age;

            if ($age < 30) $usia['<30']++;
            elseif ($age <= 40) $usia['30-40']++;
            elseif ($age <= 50) $usia['41-50']++;
            else $usia['51+']++;
        }

        return [
            'datasets' => [
                [
                    'data' => array_values($usia),
                    'backgroundColor' => [
                    '#3B82F6', // Fungsional
                    '#EF4444', // Struktural
                    '#10B981', // Pelaksana
                    '#F54927'
                ],
                ],
            ],
            'labels' => array_keys($usia),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
