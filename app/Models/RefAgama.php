<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefAgama extends Model
{
    use HasFactory;

    protected $table = 'ref_agama';
    protected $primaryKey = 'id_agama';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_agama',
        'nama_agama',
        'status_aktif',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id_agama');
    }
}
