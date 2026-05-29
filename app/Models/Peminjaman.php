<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'ruangan_id',
        'waktu_mulai',
        'waktu_selesai',
        'keperluan',
        'jenis_peminjaman',
        'approval_kaprodi',
        'approval_dekan',
        'approval_tu',
        'approved_by_kaprodi',
        'approved_by_dekan',
        'approved_by_tu',
        'status',
        'batas_approval',
        'file_sik',
        'file_proposal',
        'file_persetujuan_fasilitas',
        'dokumen_terbit_path',
        'signature_kaprodi',
        'signature_dekan',
        'stempel_tu_fakultas',
        'signature_tu_rektorat',
    ];

    protected function casts(): array
    {
        return [
            'waktu_mulai'    => 'datetime',
            'waktu_selesai'  => 'datetime',
            'batas_approval' => 'date',
        ];
    }

    // ── Relationships ─────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function chats()
    {
        return $this->hasMany(PeminjamanChat::class);
    }

    public function approverKaprodi()
    {
        return $this->belongsTo(User::class, 'approved_by_kaprodi');
    }

    public function approverDekan()
    {
        return $this->belongsTo(User::class, 'approved_by_dekan');
    }

    public function approverTu()
    {
        return $this->belongsTo(User::class, 'approved_by_tu');
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeForRuangan(Builder $query, int $ruanganId): Builder
    {
        return $query->where('ruangan_id', $ruanganId);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // ── Business Logic ────────────────────────────────────

    /**
     * Cek apakah peminjaman ini bentrok jadwal dengan peminjaman lain
     * di ruangan yang sama.
     */
    public static function hasOverlap(
        int $ruanganId,
        string $waktuMulai,
        string $waktuSelesai,
        ?int $excludeId = null
    ): bool {
        $query = static::where('ruangan_id', $ruanganId)
            ->whereNotIn('status', ['ditolak', 'dibatalkan'])
            ->where(function (Builder $q) use ($waktuMulai, $waktuSelesai) {
                $q->where(function (Builder $inner) use ($waktuMulai, $waktuSelesai) {
                    $inner->where('waktu_mulai', '<', $waktuSelesai)
                          ->where('waktu_selesai', '>', $waktuMulai);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Cek apakah sudah melewati batas waktu approval (H-3).
     */
    public function isPastApprovalDeadline(): bool
    {
        if (!$this->batas_approval) {
            return false;
        }

        return Carbon::today()->greaterThan($this->batas_approval);
    }

    /**
     * Hitung batas approval dari waktu mulai.
     */
    public static function calculateApprovalDeadline(string $waktuMulai): string
    {
        return Carbon::parse($waktuMulai)->subDays(3)->toDateString();
    }

    // ── Accessors ─────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_kaprodi' => 'Menunggu Kaprodi',
            'revisi_kaprodi'   => 'Revisi Kaprodi',
            'menunggu_dekan'   => 'Menunggu Dekan',
            'revisi_dekan'     => 'Revisi Dekan',
            'menunggu_tu'      => 'Menunggu TU',
            'revisi_tu'        => 'Revisi TU',
            'disetujui'        => 'Disetujui',
            'ditolak'          => 'Ditolak',
            'dibatalkan'       => 'Dibatalkan',
            default            => str_replace('_', ' ', ucfirst($this->status)),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu_kaprodi', 'menunggu_dekan', 'menunggu_tu' => 'yellow',
            'revisi_kaprodi', 'revisi_dekan', 'revisi_tu'       => 'orange',
            'disetujui'   => 'green',
            'ditolak'     => 'red',
            'dibatalkan'  => 'gray',
            default       => 'gray',
        };
    }
}
