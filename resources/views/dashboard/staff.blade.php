@extends('layouts.app')

@section('title', 'Dashboard Staff')

@section('content')
<div class="space-y-6">

    {{-- ── Header ──────────────────────────────────────── --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard {{ $user->role?->name ?? 'Staff' }}</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola persetujuan peminjaman ruangan. Halo, {{ $user->name }}.</p>
    </div>

    {{-- ── Stats Cards ─────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
            <div class="p-3.5 rounded-xl bg-yellow-50 text-yellow-600 mr-4">
                <i data-feather="clock" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Menunggu Approval</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $stats['pending'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
            <div class="p-3.5 rounded-xl bg-brand-50 text-brand-600 mr-4">
                <i data-feather="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Disetujui</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $stats['disetujui'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
            <div class="p-3.5 rounded-xl bg-red-50 text-red-600 mr-4">
                <i data-feather="x-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ditolak</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $stats['ditolak'] }}</p>
            </div>
        </div>
    </div>

    {{-- ── Antrean Persetujuan ──────────────────────────── --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i data-feather="inbox" class="w-5 h-5 mr-2 text-brand-600"></i> Antrean Persetujuan Ruangan
            </h2>
        </div>

        @forelse($pendingList as $p)
        <div class="border-b border-gray-50 last:border-b-0" x-data="{ showRejectForm: false }">
            <div class="p-5 sm:p-6 hover:bg-gray-50/50 transition-colors">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    {{-- Pemohon Info --}}
                    <div class="flex items-start gap-4 flex-1 min-w-0">
                        <div class="h-11 w-11 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-md shadow-brand-200">
                            {{ $p->user->initials }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-bold text-gray-900">{{ $p->user->name }}</h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $p->jenis_peminjaman === 'dalam_fakultas' ? 'bg-brand-50 text-brand-700 border border-brand-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    {{ $p->jenis_peminjaman === 'dalam_fakultas' ? 'Internal' : 'Luar Fakultas' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                NPM: {{ $p->user->npm ?? '-' }}
                                @if($p->user->prodi)
                                    &middot; {{ $p->user->prodi->name }}
                                @endif
                            </p>
                            <div class="mt-2 space-y-1">
                                <p class="text-sm font-semibold text-gray-800">{{ $p->ruangan->name }}</p>
                                <p class="text-xs text-gray-500 flex items-center">
                                    <i data-feather="calendar" class="w-3.5 h-3.5 mr-1"></i>
                                    {{ $p->waktu_mulai->format('d M Y, H:i') }} - {{ $p->waktu_selesai->format('H:i') }}
                                </p>
                                <p class="text-xs text-gray-400">Keperluan: {{ $p->keperluan }}</p>
                            </div>

                            {{-- File Lampiran --}}
                            <div class="flex items-center gap-2 mt-3 flex-wrap">
                                @if($p->file_sik)
                                <a href="{{ Storage::url($p->file_sik) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-gray-50 hover:bg-gray-100 text-xs text-gray-700 border border-gray-200 rounded-lg transition-colors">
                                    <i data-feather="file-text" class="w-3.5 h-3.5 mr-1.5 text-brand-600"></i> SIK
                                </a>
                                @endif
                                @if($p->file_proposal)
                                <a href="{{ Storage::url($p->file_proposal) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-gray-50 hover:bg-gray-100 text-xs text-gray-700 border border-gray-200 rounded-lg transition-colors">
                                    <i data-feather="book-open" class="w-3.5 h-3.5 mr-1.5 text-brand-600"></i> Proposal
                                </a>
                                @endif
                                @if($p->file_persetujuan_fasilitas)
                                <a href="{{ Storage::url($p->file_persetujuan_fasilitas) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-gray-50 hover:bg-gray-100 text-xs text-gray-700 border border-gray-200 rounded-lg transition-colors">
                                    <i data-feather="check-square" class="w-3.5 h-3.5 mr-1.5 text-brand-600"></i> CS & Satpam
                                </a>
                                @endif
                            </div>

                            {{-- Approval Status Badges --}}
                            <div class="flex items-center gap-3 mt-3">
                                <span class="inline-flex items-center text-[11px] font-medium px-2 py-1 rounded-md
                                    @if($p->approval_kaprodi === 'approved') bg-green-50 text-green-700
                                    @elseif($p->approval_kaprodi === 'rejected') bg-red-50 text-red-700
                                    @else bg-gray-50 text-gray-500
                                    @endif">
                                    <i data-feather="{{ $p->approval_kaprodi === 'approved' ? 'check' : ($p->approval_kaprodi === 'rejected' ? 'x' : 'clock') }}" class="w-3 h-3 mr-1"></i>
                                    Kaprodi: {{ ucfirst($p->approval_kaprodi) }}
                                </span>
                                <span class="inline-flex items-center text-[11px] font-medium px-2 py-1 rounded-md
                                    @if($p->approval_dekan === 'approved') bg-green-50 text-green-700
                                    @elseif($p->approval_dekan === 'rejected') bg-red-50 text-red-700
                                    @else bg-gray-50 text-gray-500
                                    @endif">
                                    <i data-feather="{{ $p->approval_dekan === 'approved' ? 'check' : ($p->approval_dekan === 'rejected' ? 'x' : 'clock') }}" class="w-3 h-3 mr-1"></i>
                                    Dekan: {{ ucfirst($p->approval_dekan) }}
                                </span>
                                <span class="inline-flex items-center text-[11px] font-medium px-2 py-1 rounded-md
                                    @if($p->approval_tu === 'approved') bg-green-50 text-green-700
                                    @elseif($p->approval_tu === 'rejected') bg-red-50 text-red-700
                                    @else bg-gray-50 text-gray-500
                                    @endif">
                                    <i data-feather="{{ $p->approval_tu === 'approved' ? 'check' : ($p->approval_tu === 'rejected' ? 'x' : 'clock') }}" class="w-3 h-3 mr-1"></i>
                                    TU: {{ ucfirst($p->approval_tu) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('peminjaman.show', $p->id) }}"
                            class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-brand-700 bg-brand-50 hover:bg-brand-600 hover:text-white rounded-xl transition-all duration-200 shadow-sm border border-brand-200 hover:border-brand-600"
                            title="Detail & Proses">
                            <i data-feather="eye" class="w-4 h-4 mr-1.5"></i> Detail & Proses
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-brand-50 flex items-center justify-center mb-4">
                <i data-feather="check-circle" class="w-8 h-8 text-brand-300"></i>
            </div>
            <h3 class="text-sm font-semibold text-gray-600">Tidak ada antrean</h3>
            <p class="text-xs text-gray-400 mt-1">Semua pengajuan sudah diproses. Tidak ada yang menunggu persetujuan saat ini.</p>
        </div>
        @endforelse

        @if($pendingList->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $pendingList->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
