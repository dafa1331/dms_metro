<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opd extends Model

{
    use HasFactory;

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

    public function documents()
    {
        return $this->hasMany(Document::class, 'opd_id');
    }

    public function getAllChildrenIds(): array
    {
        $ids = [$this->id];

        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }

        return $ids;
    }

    public function getLabelUnitTerkecilAttribute(): string
    {
        return collect([
            $this->nama_opd,
            $this->parent?->nama_opd,
            $this->parent?->parent?->nama_opd,
        ])
        ->filter()          // buang null
        ->implode(' - ');
    }

    public function getLabelHierarchyAttribute(): string
    {
        $labels = [$this->nama_opd];
        $parent = $this->parent;

        while ($parent) {
            $labels[] = $parent->nama_opd;
            $parent = $parent->parent;
        }

        return implode(' - ', $labels);
    }
}
