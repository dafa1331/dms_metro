<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RekonPegawaiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama',
            'Gelar Depan',
            'Gelar Belakang',
            'Tempat Lahir',
            'Tanggal Lahir',
            'TMT CPNS',
            'OPD Induk',
            'OPD',
            'Jabatan Aktif',
            'Jenis Jabatan',
            'Pangkat',
            'Golongan',
            'TMT Pangkat',
            'Status Pegawai',
            'Status Kepegawaian',
        ];
    }

    public function map($p): array
    {
        $opd = $p->jabatanAktif?->opd;
        return [
            $p->nip,
            $p->nama_lengkap,
            $p->gelar_depan,
            $p->gelar_belakang,
            $p->tempat_lahir,
            $p->tanggal_lahir,
            $p->kepegawaianAktif?->tmt_status?->format('Y-m-d') ?? '-',
            $opd?->parent?->nama_opd ?? $opd?->nama_opd ?? '-',
            $p->jabatanAktif?->opd?->nama_opd ?? '-',
            $p->jabatanAktif
                ? ($p->jabatanAktif->jabatan->jenis_jabatan === 'fungsional'
                    ? $p->jabatanAktif->jabatan->jenjangJabatan?->nama_jenjang . ' - ' . $p->jabatanAktif->jabatan->nama_jabatan
                    : $p->jabatanAktif->jabatan->nama_jabatan)
                : '-',
            $p->jabatanAktif?->jabatan->jenis_jabatan ?? '-',
            $p->pangkatTerakhir?->pangkat->pangkat ?? '-',
            $p->pangkatTerakhir?->pangkat->golongan ?? '-',
            $p->pangkatTerakhir?->tmt_pangkat ?? '-',
            $p->status_pegawai,
            $p->kepegawaianAktif?->status ?? '-',
        ];
    }
}
