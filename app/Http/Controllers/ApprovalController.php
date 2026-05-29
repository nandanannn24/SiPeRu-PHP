<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApprovalController extends Controller
{
    public function approve(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $user = Auth::user();
        $user->load('role');

        if ($peminjaman->isPastApprovalDeadline()) {
            $peminjaman->update(['status' => 'ditolak']);
            return back()->with('error', 'Melewati batas waktu H-3. Otomatis ditolak.');
        }

        $path = $request->file('signature')->store('dokumen_peminjaman/signatures', 'public');
        $roleName = $user->role->name;

        if ($roleName === 'Kaprodi' && $peminjaman->status === 'menunggu_kaprodi') {
            $peminjaman->approval_kaprodi = 'approved';
            $peminjaman->approved_by_kaprodi = $user->id;
            $peminjaman->signature_kaprodi = $path;
            $peminjaman->status = 'menunggu_dekan';
        } elseif ($roleName === 'Dekan 3' && $peminjaman->status === 'menunggu_dekan') {
            $peminjaman->approval_dekan = 'approved';
            $peminjaman->approved_by_dekan = $user->id;
            $peminjaman->signature_dekan = $path;
            $peminjaman->status = 'menunggu_tu';
        } elseif ($roleName === 'TU Fakultas' && $peminjaman->status === 'menunggu_tu') {
            $peminjaman->approval_tu = 'approved';
            $peminjaman->approved_by_tu = $user->id;
            $peminjaman->stempel_tu_fakultas = $path;
            $peminjaman->status = 'disetujui';
        } elseif ($roleName === 'Admin') {
            // Jika admin yang approve (sebagai TU Rektorat), simpan signaturenya
            $peminjaman->approval_kaprodi = 'approved';
            $peminjaman->approval_dekan = 'approved';
            $peminjaman->approval_tu = 'approved';
            $peminjaman->approved_by_kaprodi = $peminjaman->approved_by_kaprodi ?? $user->id;
            $peminjaman->approved_by_dekan = $peminjaman->approved_by_dekan ?? $user->id;
            $peminjaman->approved_by_tu = $peminjaman->approved_by_tu ?? $user->id;
            $peminjaman->signature_tu_rektorat = $path;
            $peminjaman->status = 'disetujui';
        } else {
            return back()->with('error', 'Anda tidak memiliki hak untuk menyetujui pada tahap ini.');
        }

        $peminjaman->save();

        // Auto-generate PDF when fully approved
        if ($peminjaman->status === 'disetujui') {
            try {
                $docService = app(DocumentService::class);
                $path = $docService->generateSurat($peminjaman);
                $peminjaman->update(['dokumen_terbit_path' => $path]);
            } catch (\Exception $e) {
                // Log error but don't fail the approval
                \Illuminate\Support\Facades\Log::error('Failed to generate PDF: ' . $e->getMessage());
            }
        }

        $msg = $peminjaman->status === 'disetujui' ? 'Peminjaman disetujui sepenuhnya. Surat otomatis diterbitkan.' : 'Approval berhasil, lanjut ke tahap berikutnya.';
        return back()->with('success', $msg);
    }

    public function revise(Request $request, Peminjaman $peminjaman)
    {
        $request->validate(['message' => 'required|string']);
        $user = Auth::user();
        $roleName = $user->role->name;

        if ($roleName === 'Kaprodi' && $peminjaman->status === 'menunggu_kaprodi') {
            $peminjaman->status = 'revisi_kaprodi';
        } elseif ($roleName === 'Dekan 3' && $peminjaman->status === 'menunggu_dekan') {
            $peminjaman->status = 'revisi_dekan';
        } elseif ($roleName === 'TU Fakultas' && $peminjaman->status === 'menunggu_tu') {
            $peminjaman->status = 'revisi_tu';
        } else {
            return back()->with('error', 'Tidak dapat meminta revisi pada tahap ini.');
        }

        $peminjaman->save();

        \App\Models\PeminjamanChat::create([
            'peminjaman_id' => $peminjaman->id,
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Permintaan revisi berhasil dikirim.');
    }

    public function reject(Request $request, Peminjaman $peminjaman)
    {
        $request->validate(['catatan_penolakan' => 'required|string']);
        $user = Auth::user();
        $roleName = $user->role->name;

        if (!in_array($peminjaman->status, ['menunggu_kaprodi', 'menunggu_dekan', 'menunggu_tu'])) {
            return back()->with('error', 'Tidak dapat menolak peminjaman ini.');
        }

        if ($roleName === 'Kaprodi') $peminjaman->approval_kaprodi = 'rejected';
        elseif ($roleName === 'Dekan 3') $peminjaman->approval_dekan = 'rejected';
        elseif ($roleName === 'TU Fakultas') $peminjaman->approval_tu = 'rejected';
        elseif ($roleName === 'Admin') {
            $peminjaman->approval_kaprodi = 'rejected';
            $peminjaman->approval_dekan = 'rejected';
            $peminjaman->approval_tu = 'rejected';
        }

        $peminjaman->status = 'ditolak';
        $peminjaman->save();

        \App\Models\PeminjamanChat::create([
            'peminjaman_id' => $peminjaman->id,
            'user_id' => $user->id,
            'message' => 'DITOLAK: ' . $request->catatan_penolakan,
        ]);

        return back()->with('success', 'Peminjaman telah ditolak.');
    }
}
