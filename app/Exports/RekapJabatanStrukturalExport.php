<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapJabatanStrukturalExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $pangkats;

    public function __construct($data, $pangkats)
    {
        $this->data = $data;
        $this->pangkats = $pangkats;
    }

    public function headings(): array
    {
        $headers = ['No', 'Nama Jabatan'];

        foreach ($this->pangkats as $pangkat) {
            $headers[] = $pangkat->golongan;
        }

        return $headers;
    }

    public function array(): array
    {
        $result = [];
        $no = 1;

        foreach ($this->data as $row) {

            $line = [
                $no++,
                $row['nama_jabatan'],
            ];

            foreach ($this->pangkats as $pangkat) {
                $line[] = $row['counts'][$pangkat->golongan] ?? 0;
            }

            $result[] = $line;
        }

        return $result;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // header bold
        ];
    }
}
