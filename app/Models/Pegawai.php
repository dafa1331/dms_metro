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
        return $this->hasOne(RiwayatJabatan::class)
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

    public function getUsiaBupAttribute()
    {
        $jabatan = $this->jabatanAktif?->jabatan;

        if (!$jabatan) {
            return null;
        }

        // Guru
        if ($jabatan->jenis_jabatan === 'fungsional'
            && str_contains(strtolower($jabatan->jenis_fungsional), 'guru')) {
            return 60;
        }

        // Fungsional
        if ($jabatan->jenis_jabatan === 'fungsional') {
            if (str_contains(strtolower($jabatan->jenjang), 'Ahli Utama')) {
                return 65;
            }

            if (str_contains(strtolower($jabatan->jenjang), 'Ahli Madya')) {
                return 60;
            }

            return 58; // muda & pertama
        }

        // Struktural
        if ($jabatan->jenis_jabatan === 'struktural') {
            if ($jabatan->eselon === 'II' || $jabatan->eselon === 'I') {
                return 60; // pimpinan tinggi
            }

            return 58; // administrator ke bawah
        }

        return 58;
    }

    public function getTmtBupAttribute()
    {
        if (!$this->tanggal_lahir || !$this->usia_bup) {
            return '-';
        }

        $tglBup = \Carbon\Carbon::parse($this->tanggal_lahir)
            ->addYears($this->usia_bup)
            ->endOfMonth();

        return $tglBup->format('Y-m-d');
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
            $this->jabatan_aktif->tmt_jabatan
        )->diff(now());

        return "{$diff->y} Th {$diff->m} Bln";
    }








}
