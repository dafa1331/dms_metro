<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

}
