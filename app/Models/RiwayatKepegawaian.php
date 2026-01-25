<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatKepegawaian extends Model
{

    protected $table = 'riwayat_kepegawaian';
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'status',
        'tmt_status',
        'nomor_sk',
        'tanggal_sk',
        'keterangan',
        'is_aktif',
    ];

    protected $casts = [
        'tmt_status' => 'date',
        'tanggal_sk' => 'date',
        'is_aktif' => 'boolean',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
