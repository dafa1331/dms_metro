<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    protected $table = 'pangkats';
    protected $primaryKey = 'id';
    use HasFactory;

    protected $fillable = [
        'golongan',
        'pangkat',
        'status_aktif',
    ];

    
    public function riwayatPangkat() {
        return $this->hasMany(RiwayatPangkat::class, 'ref_id_pangkat');
    }
}
