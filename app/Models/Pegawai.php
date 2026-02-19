<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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
    public function pendidikanTerakhir(): Attribute
    {
        return Attribute::make(
            get: function () {

                $ranking = [
                    'SD' => 1,
                    'SMP' => 2,
                    'SMA/SMK' => 3,
                    'D1' => 4,
                    'D2' => 5,
                    'D3' => 6,
                    'D4' => 7,
                    'S1' => 8,
                    'S2' => 9,
                    'S3' => 10,
                ];

                return $this->riwayatPendidikan
                    ->sortByDesc(function ($item) use ($ranking) {
                        return $ranking[$item->tingkat_pendidikan] ?? 0;
                    })
                    ->first();
            }
        );
    }

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

    public function riwayatJabatan()
    {
        return $this->hasMany(RiwayatJabatan::class, 'pegawai_id');
    }

    public function jabatanAktif()
    {
        return $this->hasOne(RiwayatJabatan::class, 'pegawai_id')
            ->where('status_aktif', 1);
    }

    public function pangkatTerakhir()
    {
        return $this->hasOne(RiwayatPangkat::class)
            ->latestOfMany('tmt_pangkat');
    }

    public function kepegawaianAktif()
    {
        return $this->hasOne(RiwayatKepegawaian::class)
            ->where('is_aktif', 1);
    }

    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class, 'pegawai_id');
    }

    public function getPendidikanTerakhirTingkatAttribute()
    {
        return $this->pendidikan_terakhir->tingkat_pendidikan ?? '-';
    }

    public function getPendidikanTerakhirJurusanAttribute()
    {
        return $this->pendidikan_terakhir->jurusan ?? '-';
    }

    public function getPendidikanTerakhirTahunLulusAttribute()
    {
        return $this->pendidikan_terakhir->tahun_lulus ?? '-';
    }

    public function getUsiaAttribute()
    {
        if (!$this->tanggal_lahir) {
            return '-';
        }

        return Carbon::parse($this->tanggal_lahir)->age;
    }

    // Hitung usia BUP berdasarkan jenis jabatan
    public function getUsiaBupAttribute()
    {
        $jabatan = $this->jabatanAktif?->jabatan;

        if (!$jabatan) return 58; // default

        // Guru fungsional
        if ($jabatan->jenis_jabatan === 'fungsional' 
            && str_contains(strtolower($jabatan->jenis_fungsional ?? ''), 'guru')) {
            return 60;
        }

        // Fungsional umum
        if ($jabatan->jenis_jabatan === 'fungsional') {
            if (str_contains(strtolower($jabatan->jenjang_jabatan_id ?? ''), '8')) return 65;
            if (str_contains(strtolower($jabatan->jenjang_jabatan_id ?? ''), '7')) return 60;
            return 58;
        }

        // Struktural
        if ($jabatan->jenis_jabatan === 'struktural') {
            if (str_starts_with($jabatan->eselon ?? '', 'I') ||
                str_starts_with($jabatan->eselon ?? '', 'II')) {
                return 60;
            }
        }

        return 58;
    }

    // TMT BUP = akhir bulan saat usia pensiun tercapai
    public function getTmtBupAttribute()
    {
        if (!$this->tanggal_lahir || !$this->usia_bup) return null;

        return Carbon::parse($this->tanggal_lahir)
            ->addYears($this->usia_bup)
            ->endOfMonth()
            ->format('Y-m-d');
    }

    // TMT Pensiun = tanggal 1 bulan berikutnya setelah BUP
    public function getTmtPensiunAttribute()
{
    if (!$this->tmt_bup) return null;

    return Carbon::parse($this->tmt_bup)
        ->addMonthNoOverflow() // <- penting
        ->startOfMonth()
        ->format('Y-m-d');
}


    public function getTanggalKontrakBerakhirAttribute()
    {
        if (!in_array($this->kepegawaianAktif?->jenis_kepegawaian, ['PPPK', 'PPPKPW'])) {
            return '-';
        }

        return $this->kepegawaianAktif?->tanggal_selesai_kontrak
            ? $this->kepegawaianAktif->tanggal_selesai_kontrak->format('Y-m-d')
            : '-';
    }

    public function getTmtAwalKerjaAttribute()
    {
        return $this->riwayatKepegawaian
            ->sortBy('tmt_status')
            ->first()
            ?->tmt_status;
    }

    public function getMasaKerjaTotalAttribute()
    {
        if (!$this->tmt_awal_kerja) return '-';

        $diff = \Carbon\Carbon::parse($this->tmt_awal_kerja)->diff(now());

        return "{$diff->y} Th {$diff->m} Bln";
    }

    public function getPangkatTerakhirAttribute()
    {
        return $this->riwayatPangkat
            ->sortByDesc('tmt_pangkat')
            ->first();
    }

    public function getMasaKerjaGolonganAttribute()
    {
        if (!$this->pangkat_terakhir) return '-';

        $diff = \Carbon\Carbon::parse(
            $this->pangkat_terakhir->tmt_pangkat
        )->diff(now());

        return "{$diff->y} Th {$diff->m} Bln";
    }

    public function getJabatanAktifAttribute()
    {
        return $this->riwayatJabatan
            ->sortByDesc('tmt_mulai')
            ->first();
    }

    public function getMasaKerjaJabatanAttribute()
    {
        if (!$this->jabatan_aktif) return '-';

        $diff = \Carbon\Carbon::parse(
            $this->jabatan_aktif->tmt_mulai
        )->diff(now());

        return "{$diff->y} Th {$diff->m} Bln";
    }

    public function getNamaJabatanLengkapAttribute(): string
    {
        $jabatanAktif = $this->jabatanAktif;

        if (! $jabatanAktif || ! $jabatanAktif->jabatan) {
            return '-';
        }

        $jabatan = $jabatanAktif->jabatan;

        if ($jabatan->jenis_jabatan === 'fungsional') {
            return trim(
                ($jabatan->jenjangJabatan?->nama_jenjang ?? '')
                . ' - '
                . $jabatan->nama_jabatan
            );
        }

        return $jabatan->nama_jabatan;
    }

    public function getNamaPangkatAttribute(): string
    {
        if (! $this->pangkatTerakhir || ! $this->pangkatTerakhir->pangkat) {
            return '-';
        }

        $p = $this->pangkatTerakhir->pangkat;

        return "{$p->pangkat}, {$p->golongan}";
    }

    public function dokumen()
    {
        return $this->hasMany(Document::class, 'nip');
    }

    public function statusAktif()
{
    return $this->hasOne(RiwayatKepegawaian::class)
        ->where('is_aktif', 1);
}

}
