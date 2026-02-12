<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Pegawai;

class JabatanChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Jabatan ASN';

    protected function getData(): array
    {
        $data = Pegawai::selectRaw('jenis_jabatan, count(*) as total')
            ->groupBy('jenis_jabatan')
            ->pluck('total', 'jenis_jabatan');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah ASN',
                    'data' => $data->values(),
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
