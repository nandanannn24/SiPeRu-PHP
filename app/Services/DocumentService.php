<?php

namespace App\Services;

use App\Models\Peminjaman;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    /**
     * Generate surat PDF dengan tanda tangan dan stempel otomatis.
     * Menggunakan placeholder #TTD1#, #TTD2#, #TTD3# dan stempel TU Fakultas.
     */
    public function generateSurat(Peminjaman $peminjaman): string
    {
        $peminjaman->load([
            'user.prodi',
            'user.fakultas',
            'ruangan.fakultas',
            'approverKaprodi',
            'approverDekan',
            'approverTu',
        ]);

        if ($peminjaman->status !== 'disetujui') {
            throw new \Exception('Surat tidak bisa dicetak karena peminjaman belum disetujui sepenuhnya.');
        }

        // Build signature data
        $signatures = $this->buildSignatureData($peminjaman);

        $data = [
            'peminjaman'     => $peminjaman,
            'signatures'     => $signatures,
            'tanggalSurat'   => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            'isLuarFakultas' => $peminjaman->jenis_peminjaman === 'luar_fakultas',
        ];

        if ($peminjaman->jenis_peminjaman === 'luar_fakultas') {
            $pdf = Pdf::loadView('pdf.surat_pengantar', $data);
            $fileName = 'surat_pengantar_' . $peminjaman->id . '.pdf';
        } else {
            $pdf = Pdf::loadView('pdf.surat_balasan', $data);
            $fileName = 'surat_balasan_' . $peminjaman->id . '.pdf';
        }

        $pdf->setPaper('A4', 'portrait');

        $path = 'dokumen_terbit/' . $fileName;
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Build signature base64 data for all approval stages.
     *
     * @return array{ttd1: ?string, ttd1_name: string, ttd2: ?string, ttd2_name: string, ttd3: ?string, ttd3_name: string, stempel_tu: ?string}
     */
    public function buildSignatureData(Peminjaman $peminjaman): array
    {
        $result = [
            'ttd1'       => null, // Kaprodi signature
            'ttd1_name'  => 'Kaprodi',
            'ttd2'       => null, // Dekan 3 signature
            'ttd2_name'  => 'Dekan III',
            'ttd3'       => null, // TU Rektorat signature (for luar_fakultas)
            'ttd3_name'  => 'TU Rektorat',
            'stempel_tu' => null, // TU Fakultas stamp (overlays TTD2)
        ];

        // #TTD1# → Kaprodi yang menyetujui
        if ($peminjaman->approverKaprodi) {
            $result['ttd1'] = $this->getFileBase64($peminjaman->signature_kaprodi);
            $result['ttd1_name'] = $peminjaman->approverKaprodi->name;
        }

        // #TTD2# → Dekan 3 yang menyetujui
        if ($peminjaman->approverDekan) {
            $result['ttd2'] = $this->getFileBase64($peminjaman->signature_dekan);
            $result['ttd2_name'] = $peminjaman->approverDekan->name;
        }

        // Stempel TU Fakultas → overlay pada area #TTD2#
        if ($peminjaman->approverTu) {
            $result['stempel_tu'] = $this->getFileBase64($peminjaman->stempel_tu_fakultas);
        }

        // #TTD3# → TU Rektorat (hanya untuk luar_fakultas yang sudah ACC final)
        if ($peminjaman->jenis_peminjaman === 'luar_fakultas' && $peminjaman->status === 'disetujui') {
            // Cari user TU Rektorat (Admin role) yang menyetujui (kalau admin approve, dia tercatat di approved_by_tu atau kita cari Admin pertama)
            $tuRektorat = User::whereHas('role', function ($q) {
                $q->where('name', 'Admin');
            })->first();

            if ($tuRektorat) {
                $result['ttd3'] = $this->getFileBase64($peminjaman->signature_tu_rektorat);
                $result['ttd3_name'] = $tuRektorat->name;
            }
        }

        return $result;
    }

    /**
     * Convert any file path from storage to base64 data URI.
     */
    private function getFileBase64(?string $path): ?string
    {
        if (!$path || !Storage::disk('public')->exists($path)) {
            return null;
        }

        $fullPath = Storage::disk('public')->path($path);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'png'          => 'image/png',
            'jpg', 'jpeg'  => 'image/jpeg',
            'webp'         => 'image/webp',
            default        => 'image/png',
        };

        $data = file_get_contents($fullPath);
        return 'data:' . $mimeType . ';base64,' . base64_encode($data);
    }

    /**
     * Get stempel/logo UPN base64 for TU Fakultas stamp overlay.
     * Uses the logo.webp from public directory.
     */
    private function getStempelBase64(): ?string
    {
        $logoPath = public_path('logo.webp');

        if (!file_exists($logoPath)) {
            // Fallback: try png
            $logoPath = public_path('logo.png');
            if (!file_exists($logoPath)) {
                return null;
            }
        }

        $extension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'png'          => 'image/png',
            'jpg', 'jpeg'  => 'image/jpeg',
            'webp'         => 'image/webp',
            default        => 'image/png',
        };

        $data = file_get_contents($logoPath);
        return 'data:' . $mimeType . ';base64,' . base64_encode($data);
    }
}
