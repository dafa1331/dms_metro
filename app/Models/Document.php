<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_document';
    public $incrementing = true;

    protected $keyType = 'int';
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

    public function opd()
        {
            return $this->belongsTo(Opd::class, 'opd_id');
        }

    public function uploader()
        {
            return $this->belongsTo(User::class, 'uploaded_by');
        }

}
