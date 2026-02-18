<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class UsersTemplateExport implements WithHeadings, WithEvents
{
    public function headings(): array
    {
        return [
            'name',
            'email',
            'password',
            'role',
            'nama_opd',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet;

                // Contoh baris contoh
                $sheet->setCellValue('A2', 'Budi Santoso');
                $sheet->setCellValue('B2', 'budi@email.com');
                $sheet->setCellValue('C2', '123456');
                $sheet->setCellValue('D2', 'kasubbag_kepegawaian');
                $sheet->setCellValue('E2', 'Dinas Pendidikan');
            },
        ];
    }
}
