<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'opd_id',   // ⬅️ WAJIB supaya bisa mass assign
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /* ===============================
     * RELATIONSHIP
     * =============================== */

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    /* ===============================
     * HELPER ROLE & PANEL
     * =============================== */

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isKasubbag(): bool
    {
        return $this->hasRole('kasubbag_kepegawaian');
    }

    /* ===============================
     * SCOPES (UNTUK QUERY FILAMENT)
     * =============================== */

    public function scopeByOpd($query)
    {
        return $query->where('opd_id', auth()->user()->opd_id);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->hasRole('admin'),
            'kasubbag' => $this->hasRole('kasubbag_kepegawaian'),
            default => false,
        };
    }
}
