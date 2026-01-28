<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatKepegawaian extends Model
{
    use HasFactory;

    protected $table = 'riwayat_kepegawaian';

    protected $fillable = [
        'pegawai_id',

        // status & jenis
        'status',               // AKTIF, PENSIUN, MUTASI_KELUAR, dll
        'jenis_kepegawaian',    // PNS, PPPK, PPPKPW

        // periode
        'tmt_status',           // mulai status
        'tmt_selesai',          // akhir kontrak (NULL untuk PNS)

        // SK
        'nomor_sk',
        'tanggal_sk',

        // tambahan
        'periode_kontrak',      // kontrak ke-1, ke-2, dst
        'keterangan',
        'is_aktif',
    ];

    protected $casts = [
        'tmt_status' => 'date',
        'tmt_selesai' => 'date',
        'tanggal_sk' => 'date',
        'is_aktif' => 'boolean',
    ];

    /* =======================
     | RELATION
     ======================= */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /* =======================
     | HELPER / ACCESSOR
     ======================= */

    /**
     * Apakah riwayat ini PPPK / PPPKPW
     */
    public function getIsPppkAttribute(): bool
    {
        return in_array($this->jenis_kepegawaian, ['PPPK', 'PPPKPW']);
    }

    /**
     * Apakah kontrak masih aktif
     */
    public function getKontrakAktifAttribute(): bool
    {
        if (!$this->is_pppk || !$this->tmt_selesai) {
            return false;
        }

        return now()->lte($this->tmt_selesai);
    }

    /**
     * Sisa kontrak (hari)
     */
    public function getSisaKontrakHariAttribute(): ?int
    {
        if (!$this->is_pppk || !$this->tmt_selesai) {
            return null;
        }

        return now()->diffInDays($this->tmt_selesai, false);
    }
}
