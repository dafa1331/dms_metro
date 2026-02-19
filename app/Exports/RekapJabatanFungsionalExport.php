<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class RekapJabatanFungsionalExport implements FromArray
{
    protected $data;
    protected $pangkats;

    public function __construct($data, $pangkats)
    {
        $this->data = $data;
        $this->pangkats = $pangkats;
    }

    public function array(): array
    {
        $header = ['No', 'Nama Jabatan'];

        foreach ($this->pangkats as $p) {
            $header[] = $p->golongan;
        }

        $rows[] = $header;

        foreach ($this->data as $index => $row) {

            $line = [
                $index+1,
                $row['nama_jabatan']
            ];

            foreach ($this->pangkats as $p) {
                $line[] = $row['counts'][$p->golongan] ?? 0;
            }

            $rows[] = $line;
        }

        return $rows;
    }
}
