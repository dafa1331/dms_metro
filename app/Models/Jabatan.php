<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $fillable = [
        'kode_jabatan',
        'nama_jabatan',
        'jenis_jabatan',
        'jenjang_jabatan_id',
        'jenis_fungsional',
        'eselon',
        'kelas_jabatan',
        'aktif',
    ];

    public function jenjangJabatan()
    {
        return $this->belongsTo(JenjangJabatan::class, 'jenjang_jabatan_id');
    }

    public function riwayatJabatanAktif()
    {
        return $this->hasOne(RiwayatJabatan::class)
            ->where('status_aktif', 1);
    }

    use HasFactory;
}
