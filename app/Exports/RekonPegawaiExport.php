<?php

namespace App\Exports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class RekonPegawaiExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    public function query()
    {
        return Pegawai::with([
            'jabatanAktif.opd.parent',
            'jabatanAktif.jabatan.jenjangJabatan',
            'pangkatTerakhir.pangkat',
            'kepegawaianAktif',
            'riwayatPendidikan',
            'pendidikan_terakhir',
        ])
        ->whereHas('jabatanAktif')
        ->whereHas('pangkatTerakhir');
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
            'Pendidikan Terakhir',
            'Tingkat',
            'Jurusan',
            'Tahun Lulus',
            'Usia',
            'TMT BUP',
            'Kontrak Berakhir',
            'Masa Kerja Total',
            'Masa Kerja Golongan',
            'Masa Kerja Jabatan',
            'Status Pegawai',
            'Status Kepegawaian',
        ];
    }

    public function map($p): array
    {
        $opd = $p->jabatanAktif?->opd;

        return [
            "'".$p->nip,
            $p->nama_lengkap,
            $p->gelar_depan,
            $p->gelar_belakang,
            $p->tempat_lahir,
            $p->tanggal_lahir,
            $p->kepegawaianAktif?->tmt_status?->format('Y-m-d') ?? '-',
            $opd?->parent?->nama_opd ?? $opd?->nama_opd ?? '-',
            $opd?->nama_opd ?? '-',
            $p->jabatanAktif
                ? ($p->jabatanAktif->jabatan->jenis_jabatan === 'fungsional'
                    ? ($p->jabatanAktif->jabatan->jenjangJabatan?->nama_jenjang ?? '') 
                        . ' - ' . $p->jabatanAktif->jabatan->nama_jabatan
                    : $p->jabatanAktif->jabatan->nama_jabatan)
                : '-',
            $p->jabatanAktif?->jabatan->jenis_jabatan ?? '-',
            $p->pangkatTerakhir?->pangkat->pangkat ?? '-',
            $p->pangkatTerakhir?->pangkat->golongan ?? '-',
            $p->pangkatTerakhir?->tmt_pangkat ?? '-',
            $p->pendidikan_terakhir
                ? $p->pendidikan_terakhir->tingkat_pendidikan . ' - ' 
                    . $p->pendidikan_terakhir->nama_sekolah
                : '-',
            $p->pendidikan_terakhir_tingkat,
            $p->pendidikan_terakhir_jurusan,
            $p->pendidikan_terakhir_tahun_lulus,
            $p->usia,
            $p->tmt_bup,
            $p->tanggal_kontrak_berakhir,
            $p->masa_kerja_total,
            $p->masa_kerja_golongan,
            $p->masa_kerja_jabatan,
            $p->status_pegawai,
            $p->kepegawaianAktif?->status ?? '-',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}