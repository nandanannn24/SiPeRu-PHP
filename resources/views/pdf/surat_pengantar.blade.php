<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar Peminjaman Rektorat</title>
    <style>
        @page {
            margin: 2.5cm 2cm 2cm 2.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #1a1a1a;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        .header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0 0 4px 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header p {
            font-size: 11pt;
            margin: 0;
            color: #333;
        }
        .content p {
            margin-bottom: 8px;
            text-align: justify;
        }
        .content ul {
            margin: 8px 0 12px 0;
            padding-left: 24px;
        }
        .content ul li {
            margin-bottom: 4px;
        }

        /* ── Signature Area ─────────────────────────────── */
        .signature-area {
            width: 100%;
            margin-top: 30px;
        }
        .signature-block {
            position: relative;
            width: 200px;
            text-align: center;
        }
        .signature-img {
            width: 140px;
            height: auto;
            margin: 8px auto;
            display: block;
        }
        .signature-placeholder {
            height: 70px;
        }
        .signature-name {
            font-weight: bold;
            border-bottom: 1px solid #1a1a1a;
            padding-bottom: 2px;
        }

        /* ── Stempel TU Fakultas (overlay on TTD2 area) ── */
        .ttd2-wrapper {
            position: relative;
            display: inline-block;
            width: 200px;
        }
        .stempel-overlay {
            position: absolute;
            top: -10px;
            left: 30px;
            width: 120px;
            height: 120px;
            opacity: 0.45;
            z-index: 999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Surat Pengantar Peminjaman Fasilitas Umum</h2>
        <p>Universitas Pembangunan Nasional "Veteran" Jawa Timur</p>
    </div>

    <div class="content">
        <p>Dengan hormat,</p>
        <p>Melalui surat ini, kami meneruskan permohonan peminjaman fasilitas umum tingkat universitas/rektorat, atas nama:</p>

        <ul>
            <li><strong>NPM:</strong> {{ $peminjaman->user->npm }}</li>
            <li><strong>Nama:</strong> {{ $peminjaman->user->name }}</li>
            <li><strong>Program Studi:</strong> {{ $peminjaman->user->prodi->name ?? '-' }}</li>
            <li><strong>Fakultas:</strong> {{ $peminjaman->user->fakultas->name ?? '-' }}</li>
        </ul>

        <p>Detail Peminjaman:</p>
        <ul>
            <li><strong>Ruangan:</strong> {{ $peminjaman->ruangan->name }}</li>
            <li><strong>Waktu Mulai:</strong> {{ \Carbon\Carbon::parse($peminjaman->waktu_mulai)->format('d F Y, H:i') }} WIB</li>
            <li><strong>Waktu Selesai:</strong> {{ \Carbon\Carbon::parse($peminjaman->waktu_selesai)->format('d F Y, H:i') }} WIB</li>
            <li><strong>Keperluan:</strong> {{ $peminjaman->keperluan }}</li>
            <li><strong>Jenis:</strong> Luar Fakultas</li>
        </ul>

        <p>Pemohon telah melampirkan berkas kelengkapan yang dibutuhkan (SIK, Proposal, dan Surat Persetujuan Fasilitas). Permohonan ini telah disetujui oleh Kaprodi, Dekan III, dan TU Fakultas terkait.</p>

        <p>Demikian surat pengantar ini dibuat untuk diproses sebagaimana mestinya.</p>

        <br>
        <p style="text-align: right;">Surabaya, {{ $tanggalSurat }}</p>

        {{-- ── 3-column signature layout ─────────────────── --}}
        <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
            <tr>
                {{-- #TTD1# — Kaprodi --}}
                <td style="width: 33%; text-align: center; vertical-align: top; padding: 0 5px;">
                    <p style="margin-bottom: 4px; font-size: 10pt;">Mengetahui,<br>Kaprodi</p>
                    @if($signatures['ttd1'])
                        <img src="{{ $signatures['ttd1'] }}" class="signature-img" alt="TTD Kaprodi">
                    @else
                        <div class="signature-placeholder"></div>
                    @endif
                    <p class="signature-name" style="font-size: 10pt;">{{ $signatures['ttd1_name'] }}</p>
                </td>

                {{-- #TTD2# — Dekan 3 + Stempel TU Fakultas overlay --}}
                <td style="width: 33%; text-align: center; vertical-align: top; padding: 0 5px;">
                    <p style="margin-bottom: 4px; font-size: 10pt;">Menyetujui,<br>Dekan III Bid. Kemahasiswaan</p>
                    <div class="ttd2-wrapper">
                        @if($signatures['stempel_tu'])
                            <img src="{{ $signatures['stempel_tu'] }}" class="stempel-overlay" alt="Stempel TU Fakultas">
                        @endif
                        @if($signatures['ttd2'])
                            <img src="{{ $signatures['ttd2'] }}" class="signature-img" alt="TTD Dekan 3">
                        @else
                            <div class="signature-placeholder"></div>
                        @endif
                    </div>
                    <p class="signature-name" style="font-size: 10pt;">{{ $signatures['ttd2_name'] }}</p>
                </td>

                {{-- #TTD3# — TU Rektorat (jika luar_fakultas) --}}
                <td style="width: 33%; text-align: center; vertical-align: top; padding: 0 5px;">
                    @if($isLuarFakultas)
                        <p style="margin-bottom: 4px; font-size: 10pt;">Diproses oleh,<br>TU Rektorat</p>
                        @if($signatures['ttd3'])
                            <img src="{{ $signatures['ttd3'] }}" class="signature-img" alt="TTD TU Rektorat">
                        @else
                            <div class="signature-placeholder"></div>
                        @endif
                        <p class="signature-name" style="font-size: 10pt;">{{ $signatures['ttd3_name'] }}</p>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
