@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Peminjaman</h1>
            <p class="text-gray-500 text-sm mt-1">Seluruh riwayat pengajuan peminjaman ruangan Anda.</p>
        </div>
        <a href="{{ route('peminjaman.create') }}"
           class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-brand-600 to-brand-700 rounded-xl font-semibold text-sm text-white shadow-lg shadow-brand-200 hover:shadow-xl hover:from-brand-700 hover:to-brand-800 transition-all duration-200">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i> Ajukan Baru
        </a>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ruangan</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keperluan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($peminjamans as $p)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $p->ruangan->name }}</div>
                            @if($p->ruangan->fakultas)
                            <div class="text-xs text-gray-400 mt-0.5">{{ $p->ruangan->fakultas->name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $p->waktu_mulai->format('d M Y') }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $p->waktu_mulai->format('H:i') }} - {{ $p->waktu_selesai->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $p->jenis_peminjaman === 'dalam_fakultas' ? 'bg-brand-50 text-brand-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ $p->jenis_peminjaman === 'dalam_fakultas' ? 'Internal' : 'Luar Fakultas' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($p->status === 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                @elseif($p->status === 'disetujui') bg-green-100 text-green-800 border border-green-200
                                @elseif($p->status === 'ditolak') bg-red-100 text-red-800 border border-red-200
                                @else bg-gray-100 text-gray-700 border border-gray-200
                                @endif">
                                {{ $p->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 truncate max-w-xs">{{ $p->keperluan }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-3">
                                <i data-feather="inbox" class="w-7 h-7 text-gray-300"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-500">Belum ada riwayat peminjaman</p>
                            <p class="text-xs text-gray-400 mt-1">Mulai dengan mengajukan peminjaman ruangan baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($peminjamans->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $peminjamans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
