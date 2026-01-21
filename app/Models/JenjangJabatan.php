<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenjangJabatan extends Model
{
    protected $fillable = [
        'nama_jenjang',
        'jenis_jabatan',
        'keterangan',
    ];

    public function jabatans()
    {
        return $this->hasMany(Jabatan::class, 'id');
    }

    use HasFactory;
}
