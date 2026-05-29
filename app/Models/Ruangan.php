<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Ruangan extends Model
{
    use HasFactory;

    protected $fillable = ['fakultas_id', 'name', 'status', 'jenis', 'gedung'];

    // ── Relationships ─────────────────────────────────────

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'tersedia');
    }

    // ── Helpers ───────────────────────────────────────────

    /**
     * Apakah ini fasilitas umum (tidak dimiliki fakultas manapun)?
     */
    public function isFasilitasUmum(): bool
    {
        return $this->fakultas_id === null;
    }
}
