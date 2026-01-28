<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pendidikan';

    protected $fillable = [
        'pegawai_id',
        'tingkat_pendidikan',
        'nama_sekolah',
        'jurusan',
        'tahun_masuk',
        'tahun_lulus',
        'nomor_ijazah',
        'tanggal_ijazah',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

}
