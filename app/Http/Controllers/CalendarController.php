<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Hanya menampilkan jadwal yang statusnya disetujui (Approved)
        $peminjamans = Peminjaman::with('user.prodi', 'ruangan')
            ->where('status', 'disetujui')
            ->get();

        $events = $peminjamans->map(function ($peminjaman) {
            return [
                'id' => $peminjaman->id,
                'title' => 'Booked - ' . $peminjaman->ruangan->name,
                'start' => $peminjaman->waktu_mulai,
                'end' => $peminjaman->waktu_selesai,
                'ruangan' => $peminjaman->ruangan->name,
                'jenis_peminjaman' => $peminjaman->jenis_peminjaman,
                'color' => $peminjaman->jenis_peminjaman === 'dalam_fakultas' ? '#10b981' : '#ef4444', // Green for internal, Red for external
            ];
        });

        return response()->json([
            'message' => 'Berhasil mengambil data kalender',
            'data' => $events
        ]);
    }
}
