<?php

namespace App\Imports;

use App\Models\Pegawai;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class PegawaiImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        return new Pegawai([
            'nip' => $row['nip'],
            'nama_lengkap' => $row['nama_lengkap'],
            'gelar_depan' => $row['gelar_depan'],
            'gelar_belakang' => $row['gelar_belakang'],
            'tempat_lahir' => $row['tempat_lahir'],
            'tanggal_lahir' => Carbon::parse($row['tanggal_lahir']),
            'jenis_kelamin' => $row['jenis_kelamin'],
            'id_agama' => $row['id_agama'],
            'email' => $row['email'],
            'no_hp' => $row['no_hp'],
            'alamat' => $row['alamat'],
            'status_pegawai' => $row['status_pegawai'],
        ]);
    }

    public function uniqueBy()
    {
        return 'nip'; // supaya tidak double
    }
}