<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPangkat extends Model
{
    protected $table = 'riwayat_pangkat';
    
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'ref_id_pangkat',
        'tmt_pangkat',
        'nomor_sk',
        'tanggal_sk',
        'dokumen_sk',
        'keterangan',
        'is_aktif',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function Pangkat()
    {
        return $this->belongsTo(Pangkat::class, 'ref_id_pangkat');
    }
}
