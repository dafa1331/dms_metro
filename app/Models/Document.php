<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_document';
    public $timestamps = false;

    protected $fillable = [
        'opd_id',
        'nip',
        'type',
        'file_name',
        'original_name',
        'size',
        'mime',
        'uploaded_at',
        'catatan',
        'status_dokumen',
        'tanggal_verif',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'tanggal_verif' => 'datetime',
    ];

}
