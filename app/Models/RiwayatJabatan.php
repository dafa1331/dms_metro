<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\JenjangJabatan;

class RiwayatJabatan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_jabatan';

    protected $fillable = [
        'pegawai_id',
        'jabatan_id',
        'opd_id',
        'jenis_jabatan',
        'parent_jabatan_id',
        'tmt_mulai',
        'tmt_selesai',
        'nomor_sk',
        'tanggal_sk',
        'status_aktif',
    ];

    protected $casts = [
        'tmt_mulai'   => 'date',
        'tmt_selesai' => 'date',
        'tanggal_sk'  => 'date',
        'status_aktif'=> 'boolean',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function parentJabatan()
    {
        return $this->belongsTo(self::class, 'parent_jabatan_id');
    }

    public function getNamaJabatanLengkapAttribute()
    {
        if (! $this->jabatan) {
            return '-';
        }

        // Jabatan fungsional → pakai jenjang
        if ($this->jabatan->jenis_jabatan === 'fungsional') {
            return trim(
                $this->jabatan->nama_jabatan . ' ' .
                ($this->jenjangJabatan->nama_jenjang ?? '')
            );
        }

        // Jabatan struktural
        return $this->jabatan->nama_jabatan;
    }
    
    public function jenjangJabatan()
    {
        return $this->belongsTo(
            JenjangJabatan::class,
            'jenjang_jabatan_id' // ← pastikan sesuai nama kolom
        );
    }

}
