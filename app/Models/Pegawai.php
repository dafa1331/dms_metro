<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $fillable = [
        'nip',
        'nama_lengkap',
        'gelar_depan',
        'gelar_belakang',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'id_agama',
        'email',
        'no_hp',
        'alamat',
        'status_pegawai',
    ];

    public function agama()
    {
        return $this->belongsTo(RefAgama::class, 'id_agama');
    }

    public function riwayatKepegawaian()
    {
        return $this->hasMany(\App\Models\RiwayatKepegawaian::class);
    }

    public function riwayatPangkat()
    {
        return $this->hasMany(RiwayatPangkat::class);
    }
}
