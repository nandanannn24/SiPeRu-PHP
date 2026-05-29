<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'peminjaman_id',
        'user_id',
        'message',
        'attachment_path',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
