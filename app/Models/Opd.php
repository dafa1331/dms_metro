<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opd extends Model

{
     protected $fillable = [
        'kode_opd',
        'nama_opd',
        'jenis_opd',
        'parent_id',
        'level',
        'urutan',
        'aktif',
    ];

     public function parent()
    {
        return $this->belongsTo(Opd::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Opd::class, 'parent_id');
    }
    
    use HasFactory;
}
