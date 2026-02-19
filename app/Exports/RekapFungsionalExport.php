<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class RekapFungsionalExport implements FromArray
{
    protected $data;
    protected $jenjang;
    protected $bulan;
    protected $tahun;

    public function __construct($data, $jenjang, $bulan, $tahun)
    {
        $this->data = $data;
        $this->jenjang = $jenjang;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function array(): array
    {
        $rows[] = [
            'REKAP JABATAN FUNGSIONAL',
            'Bulan: ' . $this->bulan . '/' . $this->tahun
        ];

        $header = ['No', 'Nama Jabatan'];
        foreach ($this->jenjang as $j) {
            $header[] = $j;
        }
        $header[] = 'Total';

        $rows[] = $header;

        $no = 1;

        foreach ($this->data as $jabatan => $row) {
            $line = [$no++, $jabatan];

            foreach ($this->jenjang as $j) {
                $line[] = $row[$j];
            }

            $line[] = $row['total'];

            $rows[] = $line;
        }

        return $rows;
    }
}
