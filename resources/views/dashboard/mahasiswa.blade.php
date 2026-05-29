@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="space-y-6">

    {{-- ── Header ──────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Selamat datang, {{ $user->name }}. Pantau status pengajuan ruangan Anda.</p>
        </div>
        <a href="{{ route('peminjaman.create') }}"
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-brand-600 to-brand-700 rounded-xl font-bold text-sm text-white shadow-lg shadow-brand-200 hover:shadow-xl hover:from-brand-700 hover:to-brand-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all duration-200">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Ajukan Peminjaman
        </a>
    </div>

    {{-- ── Bento Grid Layout ─────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        
        {{-- Widget 1: Statistics (Col-span 1) --}}
        <div class="md:col-span-1 bg-gradient-to-br from-brand-700 to-brand-900 rounded-3xl p-8 text-white shadow-xl shadow-brand-200 flex flex-col justify-between relative overflow-hidden h-[450px] lg:h-[500px]">
            <div class="absolute -right-16 -top-16 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-brand-500/30 rounded-full blur-2xl"></div>

            <div class="relative z-10 flex-1 flex flex-col">
                <div class="mb-auto">
                    <h3 class="text-brand-100 font-semibold mb-6 flex items-center">
                        <i data-feather="pie-chart" class="w-5 h-5 mr-2"></i> Ringkasan Anda
                    </h3>
                    <div class="bg-white/10 rounded-2xl p-5 backdrop-blur-sm border border-white/10">
                        <p class="text-brand-100 text-sm font-medium mb-1">Total Ajuan</p>
                        <p class="text-5xl font-bold tracking-tight">{{ $stats['total'] }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm border border-white/10">
                        <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center mb-2">
                            <i data-feather="check" class="w-4 h-4 text-green-300"></i>
                        </div>
                        <p class="text-brand-200 text-xs mb-1">Disetujui</p>
                        <p class="text-2xl font-bold text-green-300">{{ $stats['disetujui'] }}</p>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm border border-white/10">
                        <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center mb-2">
                            <i data-feather="x" class="w-4 h-4 text-red-300"></i>
                        </div>
                        <p class="text-brand-200 text-xs mb-1">Ditolak</p>
                        <p class="text-2xl font-bold text-red-300">{{ $stats['ditolak'] }}</p>
                    </div>
                </div>
                
                <div class="mt-4 bg-white/10 rounded-2xl p-4 backdrop-blur-sm border border-white/10 flex justify-between items-center">
                    <span class="text-brand-200 text-xs">Menunggu</span>
                    <span class="text-lg font-bold text-yellow-300">{{ $stats['pending'] }}</span>
                </div>
            </div>
        </div>

        {{-- Widget 2: FullCalendar (Col-span 2 or 3) --}}
        <div class="md:col-span-2 lg:col-span-3 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col h-[450px] lg:h-[500px]">
             <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                 <i data-feather="calendar" class="w-5 h-5 mr-2 text-brand-600"></i> Jadwal Ruangan Terisi
             </h3>
             <div id="calendar" class="flex-grow overflow-hidden text-sm"></div>
        </div>
    </div>

    {{-- ── Riwayat Pengajuan ───────────────────────────── --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i data-feather="file-text" class="w-5 h-5 mr-2 text-brand-600"></i> Riwayat Pengajuan Terakhir
            </h2>
        </div>

        @forelse($peminjamans as $p)
        <a href="{{ route('peminjaman.show', $p->id) }}" class="block border-b border-gray-50 last:border-b-0 hover:bg-gray-50/50 transition-colors">
            <div class="p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="text-sm font-bold text-gray-900">{{ $p->ruangan->name }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if(in_array($p->status, ['menunggu_kaprodi', 'menunggu_dekan', 'menunggu_tu'])) bg-yellow-100 text-yellow-800 border border-yellow-200
                                @elseif(str_contains($p->status, 'revisi')) bg-orange-100 text-orange-800 border border-orange-200
                                @elseif($p->status === 'disetujui') bg-green-100 text-green-800 border border-green-200
                                @elseif($p->status === 'ditolak') bg-red-100 text-red-800 border border-red-200
                                @else bg-gray-100 text-gray-700 border border-gray-200
                                @endif">
                                {{ $p->status_label }}
                            </span>
                        </div>
                        <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 text-xs text-gray-500">
                            <span class="flex items-center">
                                <i data-feather="calendar" class="w-3.5 h-3.5 mr-1"></i>
                                {{ $p->waktu_mulai->format('d M Y, H:i') }} - {{ $p->waktu_selesai->format('H:i') }}
                            </span>
                            <span class="flex items-center">
                                <i data-feather="tag" class="w-3.5 h-3.5 mr-1"></i>
                                {{ $p->jenis_peminjaman === 'dalam_fakultas' ? 'Dalam Fakultas' : 'Luar Fakultas' }}
                            </span>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-400 truncate">{{ $p->keperluan }}</p>
                    </div>

                    {{-- Approval Progress --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                @if($p->approval_kaprodi === 'approved') bg-brand-100 text-brand-700
                                @elseif($p->approval_kaprodi === 'rejected') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-400
                                @endif">
                                <i data-feather="{{ $p->approval_kaprodi === 'approved' ? 'check' : ($p->approval_kaprodi === 'rejected' ? 'x' : 'clock') }}" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[10px] text-gray-400">Kaprodi</span>
                        </div>
                        <div class="w-6 h-px bg-gray-200"></div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                @if($p->approval_dekan === 'approved') bg-brand-100 text-brand-700
                                @elseif($p->approval_dekan === 'rejected') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-400
                                @endif">
                                <i data-feather="{{ $p->approval_dekan === 'approved' ? 'check' : ($p->approval_dekan === 'rejected' ? 'x' : 'clock') }}" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[10px] text-gray-400">Dekan 3</span>
                        </div>
                        <div class="w-6 h-px bg-gray-200"></div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                @if($p->approval_tu === 'approved') bg-brand-100 text-brand-700
                                @elseif($p->approval_tu === 'rejected') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-400
                                @endif">
                                <i data-feather="{{ $p->approval_tu === 'approved' ? 'check' : ($p->approval_tu === 'rejected' ? 'x' : 'clock') }}" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[10px] text-gray-400">TU</span>
                        </div>
                        <i data-feather="chevron-right" class="w-5 h-5 text-gray-400 ml-2"></i>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="p-12 text-center">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                <i data-feather="inbox" class="w-8 h-8 text-gray-300"></i>
            </div>
            <h3 class="text-sm font-semibold text-gray-600">Belum ada pengajuan</h3>
            <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">Anda belum pernah mengajukan peminjaman ruangan. Klik tombol di atas untuk memulai.</p>
        </div>
        @endforelse

        @if($peminjamans->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $peminjamans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: '100%',
            headerToolbar: {
                left: 'title',
                right: 'prev,next today'
            },
            events: function(info, successCallback, failureCallback) {
                fetch('{{ route("api.kalender") }}')
                    .then(response => response.json())
                    .then(data => {
                        successCallback(data.data);
                    })
                    .catch(error => {
                        console.error('Error fetching calendar data:', error);
                        failureCallback(error);
                    });
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false,
                hour12: false
            }
        });
        calendar.render();
    });
</script>
@endpush
