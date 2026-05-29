<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Route dashboard berdasarkan role user.
     */
    public function index()
    {
        $user = Auth::user();
        $user->load('role', 'fakultas', 'prodi');

        if ($user->isStaff()) {
            return $this->staffDashboard($user);
        }

        return $this->mahasiswaDashboard($user);
    }

    /**
     * Dashboard Mahasiswa: riwayat pengajuan sendiri.
     */
    private function mahasiswaDashboard($user)
    {
        $peminjamans = Peminjaman::with(['ruangan.fakultas'])
            ->forUser($user->id)
            ->latest()
            ->paginate(10);

        $stats = [
            'total'     => Peminjaman::forUser($user->id)->count(),
            'pending'   => Peminjaman::forUser($user->id)->whereNotIn('status', ['disetujui', 'ditolak', 'dibatalkan'])->count(),
            'disetujui' => Peminjaman::forUser($user->id)->approved()->count(),
            'ditolak'   => Peminjaman::forUser($user->id)->where('status', 'ditolak')->count(),
        ];

        return view('dashboard.mahasiswa', compact('user', 'peminjamans', 'stats'));
    }

    /**
     * Dashboard Staff: antrean persetujuan.
     */
    private function staffDashboard($user)
    {
        $query = Peminjaman::with(['user.prodi', 'user.fakultas', 'ruangan'])->latest();
        $roleName = $user->role->name;

        if ($roleName === 'Kaprodi') {
            $query->whereIn('status', ['menunggu_kaprodi', 'revisi_kaprodi'])
                  ->whereHas('user', function ($q) use ($user) {
                      $q->where('prodi_id', $user->prodi_id);
                  });
        } elseif ($roleName === 'Dekan 3') {
            $query->whereIn('status', ['menunggu_dekan', 'revisi_dekan'])
                  ->whereHas('user', function ($q) use ($user) {
                      $q->where('fakultas_id', $user->fakultas_id);
                  });
        } elseif ($roleName === 'TU Fakultas') {
            $query->whereIn('status', ['menunggu_tu', 'revisi_tu'])
                  ->whereHas('user', function ($q) use ($user) {
                      $q->where('fakultas_id', $user->fakultas_id);
                  });
        }

        $pendingList = $query->paginate(15);

        // Stats
        $statsQuery = Peminjaman::query();
        if ($roleName === 'Kaprodi') {
            $statsQuery->whereHas('user', function ($q) use ($user) { $q->where('prodi_id', $user->prodi_id); });
        } elseif (in_array($roleName, ['Dekan 3', 'TU Fakultas'])) {
            $statsQuery->whereHas('user', function ($q) use ($user) { $q->where('fakultas_id', $user->fakultas_id); });
        }

        $stats = [
            'pending'   => (clone $statsQuery)->whereNotIn('status', ['disetujui', 'ditolak', 'dibatalkan'])->count(),
            'disetujui' => (clone $statsQuery)->approved()->count(),
            'ditolak'   => (clone $statsQuery)->where('status', 'ditolak')->count(),
        ];

        return view('dashboard.staff', compact('user', 'pendingList', 'stats'));
    }
}
