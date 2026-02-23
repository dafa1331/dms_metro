<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormasiJabatan extends Model
{
    use HasFactory;
    protected $table = 'formasi_jabatan';

    protected $fillable = [
        'opd_id',
        'jabatan_id',
        'jumlah_formasi',
        'dasar_sk',
        'tanggal_sk'
    ];

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function terisi()
    {
        return RiwayatJabatan::where('opd_id', $this->opd_id)
            ->where('jabatan_id', $this->jabatan_id)
            ->where('status_aktif', 1)
            ->whereNull('tmt_selesai')
            ->count();
    }
}
