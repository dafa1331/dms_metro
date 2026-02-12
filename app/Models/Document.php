<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        'temp_path',
        'is_riwayat',
        'keterangan_riwayat'
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
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('opd_id', auth()->user()->opd_id);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nip');
    }

}
