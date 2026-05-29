<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'npm',
        'role_id',
        'fakultas_id',
        'prodi_id',
        'angkatan',
        'signature_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relationships ─────────────────────────────────────

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    // ── Role Helpers ──────────────────────────────────────

    public function isMahasiswa(): bool
    {
        return $this->role?->name === 'Mahasiswa';
    }

    public function isDosen(): bool
    {
        return $this->role?->name === 'Dosen';
    }

    public function isTendik(): bool
    {
        return $this->role?->name === 'Tendik';
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'Admin';
    }

    public function isKaprodi(): bool
    {
        return $this->role?->name === 'Kaprodi';
    }

    public function isDekan3(): bool
    {
        return $this->role?->name === 'Dekan 3';
    }

    public function isTuFakultas(): bool
    {
        return $this->role?->name === 'TU Fakultas';
    }

    public function isStaff(): bool
    {
        return $this->isKaprodi()
            || $this->isDekan3()
            || $this->isTuFakultas()
            || $this->isDosen()
            || $this->isTendik()
            || $this->isAdmin();
    }

    // ── Utilities ─────────────────────────────────────────

    /**
     * Cek apakah user berasal dari fakultas tertentu.
     */
    public function isFromFakultas(?int $fakultasId): bool
    {
        if ($fakultasId === null || $this->fakultas_id === null) {
            return false;
        }

        return (int) $this->fakultas_id === (int) $fakultasId;
    }

    /**
     * Ambil inisial nama untuk avatar.
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }

        return $initials ?: '?';
    }
}
