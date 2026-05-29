<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    /**
     * Daftar peminjaman milik user yang sedang login.
     */
    public function index()
    {
        $peminjamans = Peminjaman::with(['ruangan.fakultas'])
            ->forUser(Auth::id())
            ->latest()
            ->paginate(10);

        return view('peminjaman.index', compact('peminjamans'));
    }

    /**
     * Tampilkan form pengajuan peminjaman.
     */
    public function create()
    {
        $ruangans = Ruangan::with('fakultas')
            ->available()
            ->orderBy('name')
            ->get();

        $userFakultasId = Auth::user()->fakultas_id;

        return view('peminjaman.form', compact('ruangans', 'userFakultasId'));
    }

    /**
     * Simpan pengajuan peminjaman baru.
     */
    public function store(Request $request)
    {
        $minDate = Carbon::now()->addDays(14)->startOfDay()->toDateTimeString();

        // ── Validasi dasar ────────────────────────────────
        $rules = [
            'ruangan_id'    => 'required|exists:ruangans,id',
            'waktu_mulai'   => ['required', 'date', 'after_or_equal:' . $minDate],
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'keperluan'     => 'required|string|max:500',
        ];

        $messages = [
            'waktu_mulai.after_or_equal' => 'Peminjaman wajib diajukan minimal H-14 (2 minggu) sebelum acara.',
            'waktu_selesai.after'        => 'Waktu selesai harus setelah waktu mulai.',
        ];

        $request->validate($rules, $messages);

        $user    = Auth::user();
        $ruangan = Ruangan::findOrFail($request->ruangan_id);

        // ── Tentukan jenis peminjaman ─────────────────────
        $isInternalFakultas = $ruangan->fakultas_id !== null
            && $user->isFromFakultas($ruangan->fakultas_id);

        $jenisPeminjaman = $isInternalFakultas ? 'dalam_fakultas' : 'luar_fakultas';

        // ── Validasi file ─────────────
        $request->validate([
            'file_sik'                      => 'required|file|mimes:pdf|max:5120',
            'file_proposal'                 => 'required|file|mimes:pdf|max:5120',
            'file_persetujuan_fasilitas'    => 'required|file|mimes:pdf|max:5120',
        ], [
            'file_sik.required'                      => 'Surat Izin Kegiatan (SIK) wajib diupload.',
            'file_proposal.required'                 => 'Proposal/TOR wajib diupload.',
            'file_persetujuan_fasilitas.required'    => 'Surat Persetujuan Fasilitas (CS & Satpam) wajib diupload.',
            'file_sik.mimes'                         => 'File SIK harus berformat PDF.',
            'file_proposal.mimes'                    => 'File Proposal harus berformat PDF.',
            'file_persetujuan_fasilitas.mimes'       => 'File Persetujuan Fasilitas harus berformat PDF.',
            'file_sik.max'                           => 'File SIK maksimal 5MB.',
            'file_proposal.max'                      => 'File Proposal maksimal 5MB.',
            'file_persetujuan_fasilitas.max'         => 'File Persetujuan maksimal 5MB.',
        ]);

        // ── Cek bentrok jadwal ────────────────────────────
        if (Peminjaman::hasOverlap($ruangan->id, $request->waktu_mulai, $request->waktu_selesai)) {
            return back()->withInput()->withErrors([
                'waktu_mulai' => 'Ruangan sudah dipesan pada rentang waktu yang Anda pilih. Silakan pilih waktu lain.',
            ]);
        }

        // ── Proses upload file ────────────────────────────
        $filePaths = [
            'file_sik'                      => null,
            'file_proposal'                 => null,
            'file_persetujuan_fasilitas'    => null,
        ];

        foreach ($filePaths as $key => &$path) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('dokumen_peminjaman', 'public');
            }
        }
        unset($path);

        // ── Hitung batas approval (H-3) ───────────────────
        $batasApproval = Peminjaman::calculateApprovalDeadline($request->waktu_mulai);

        // ── Simpan data peminjaman ────────────────────────
        Peminjaman::create([
            'user_id'                    => $user->id,
            'ruangan_id'                 => $ruangan->id,
            'waktu_mulai'                => $request->waktu_mulai,
            'waktu_selesai'              => $request->waktu_selesai,
            'keperluan'                  => $request->keperluan,
            'jenis_peminjaman'           => $jenisPeminjaman,
            'approval_kaprodi'           => 'pending',
            'approval_dekan'             => 'pending',
            'approval_tu'                => 'pending',
            'status'                     => 'menunggu_kaprodi',
            'batas_approval'             => $batasApproval,
            'file_sik'                   => $filePaths['file_sik'],
            'file_proposal'              => $filePaths['file_proposal'],
            'file_persetujuan_fasilitas' => $filePaths['file_persetujuan_fasilitas'],
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Pengajuan peminjaman berhasil dibuat. Silakan pantau status di dashboard.');
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['ruangan.fakultas', 'user', 'chats.user.role']);
        return view('peminjaman.show', compact('peminjaman'));
    }

    public function storeChat(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if (!$request->message && !$request->hasFile('attachment')) {
            return back()->with('error', 'Pesan atau file tidak boleh kosong.');
        }

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('dokumen_peminjaman/revisi', 'public');
            
            // If student uploads a file, change status back to waiting
            $user = Auth::user();
            if ($user->role->name === 'Mahasiswa') {
                if ($peminjaman->status === 'revisi_kaprodi') {
                    $peminjaman->status = 'menunggu_kaprodi';
                } elseif ($peminjaman->status === 'revisi_dekan') {
                    $peminjaman->status = 'menunggu_dekan';
                } elseif ($peminjaman->status === 'revisi_tu') {
                    $peminjaman->status = 'menunggu_tu';
                }
                $peminjaman->save();
            }
        }

        \App\Models\PeminjamanChat::create([
            'peminjaman_id' => $peminjaman->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment_path' => $path,
        ]);

        return back()->with('success', 'Pesan terkirim.');
    }

    public function cetakSurat(Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'disetujui') {
            abort(403, 'Surat belum tersedia. Peminjaman belum disetujui sepenuhnya.');
        }

        $peminjaman->load([
            'user.prodi',
            'user.fakultas',
            'ruangan.fakultas',
            'approverKaprodi',
            'approverDekan',
            'approverTu',
        ]);

        // Use DocumentService to generate and stream
        $docService = app(\App\Services\DocumentService::class);

        // Build signature data
        $signatures = $docService->buildSignatureData($peminjaman);

        $data = [
            'peminjaman'     => $peminjaman,
            'signatures'     => $signatures,
            'tanggalSurat'   => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            'isLuarFakultas' => $peminjaman->jenis_peminjaman === 'luar_fakultas',
        ];

        $viewName = $peminjaman->jenis_peminjaman === 'luar_fakultas'
            ? 'pdf.surat_pengantar'
            : 'pdf.surat_balasan';

        $filePrefix = $peminjaman->jenis_peminjaman === 'luar_fakultas'
            ? 'Surat_Pengantar_'
            : 'Surat_Balasan_';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream($filePrefix . ($peminjaman->user->npm ?? $peminjaman->id) . '.pdf');
    }
}
