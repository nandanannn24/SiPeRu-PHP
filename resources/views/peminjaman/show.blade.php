@extends('layouts.app')

@section('title', 'Detail Pengajuan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-brand-600 transition-colors">
            <i data-feather="arrow-left" class="w-4 h-4 mr-1.5"></i> Kembali ke Dashboard
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 text-green-800 rounded-xl border border-green-200">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-red-50 text-red-800 rounded-xl border border-red-200">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Info Peminjaman --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Peminjaman</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs">Ruangan</p>
                        <p class="font-semibold text-gray-800">{{ $peminjaman->ruangan->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Waktu</p>
                        <p class="font-medium text-gray-800">{{ $peminjaman->waktu_mulai->format('d M Y, H:i') }} - {{ $peminjaman->waktu_selesai->format('H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 mt-1 rounded-full text-xs font-semibold
                            @if(in_array($peminjaman->status, ['menunggu_kaprodi', 'menunggu_dekan', 'menunggu_tu'])) bg-yellow-100 text-yellow-800
                            @elseif(str_contains($peminjaman->status, 'revisi')) bg-orange-100 text-orange-800
                            @elseif($peminjaman->status === 'disetujui') bg-green-100 text-green-800
                            @elseif($peminjaman->status === 'ditolak') bg-red-100 text-red-800
                            @endif">
                            {{ $peminjaman->status_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Pemohon</p>
                        <p class="font-medium text-gray-800">{{ $peminjaman->user->name }} ({{ $peminjaman->user->npm }})</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Keperluan</p>
                        <p class="text-gray-700">{{ $peminjaman->keperluan }}</p>
                    </div>
                </div>

                {{-- Approval Buttons for Approvers --}}
                @php
                    $role = auth()->user()->role->name;
                    $canApprove = false;
                    $canRevise = false;
                    if ($role === 'Kaprodi' && $peminjaman->status === 'menunggu_kaprodi') { $canApprove = true; $canRevise = true; }
                    if ($role === 'Dekan 3' && $peminjaman->status === 'menunggu_dekan') { $canApprove = true; $canRevise = true; }
                    if ($role === 'TU Fakultas' && $peminjaman->status === 'menunggu_tu') { $canApprove = true; $canRevise = true; }
                    if ($role === 'Admin' && in_array($peminjaman->status, ['menunggu_kaprodi', 'menunggu_dekan', 'menunggu_tu'])) { $canApprove = true; }
                @endphp

                @if($canApprove)
                <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                    <form action="{{ route('peminjaman.approve', $peminjaman) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Upload {{ $role === 'TU Fakultas' ? 'Stempel' : 'Tanda Tangan' }} (Wajib)
                            </label>
                            <input type="file" name="signature" required accept="image/png, image/jpeg, image/jpg" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 border border-gray-200 rounded-lg bg-gray-50 p-1 cursor-pointer">
                            @error('signature')
                                <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl text-sm transition-colors">
                            <i data-feather="check" class="w-4 h-4 inline-block mr-1"></i> Setujui
                        </button>
                    </form>

                    <div x-data="{ showReject: false, showRevise: false }">
                        <button @click="showRevise = !showRevise; showReject = false" type="button" class="w-full py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 font-bold rounded-xl text-sm transition-colors mb-3">
                            <i data-feather="message-circle" class="w-4 h-4 inline-block mr-1"></i> Minta Revisi
                        </button>
                        
                        <div x-show="showRevise" class="p-3 bg-orange-50 rounded-xl mb-3 border border-orange-200" x-cloak>
                            <form action="{{ route('peminjaman.revise', $peminjaman) }}" method="POST">
                                @csrf
                                <textarea name="message" rows="2" class="w-full text-sm rounded-lg border-gray-300 mb-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Catatan revisi untuk mahasiswa..." required></textarea>
                                <button type="submit" class="w-full py-1.5 bg-orange-600 text-white rounded-lg text-xs font-bold hover:bg-orange-700">Kirim Revisi</button>
                            </form>
                        </div>

                        <button @click="showReject = !showReject; showRevise = false" type="button" class="w-full py-2 bg-red-100 hover:bg-red-200 text-red-700 font-bold rounded-xl text-sm transition-colors">
                            <i data-feather="x" class="w-4 h-4 inline-block mr-1"></i> Tolak
                        </button>

                        <div x-show="showReject" class="p-3 bg-red-50 rounded-xl mt-3 border border-red-200" x-cloak>
                            <form action="{{ route('peminjaman.reject', $peminjaman) }}" method="POST">
                                @csrf
                                <textarea name="catatan_penolakan" rows="2" class="w-full text-sm rounded-lg border-gray-300 mb-2 focus:ring-red-500 focus:border-red-500" placeholder="Alasan penolakan..." required></textarea>
                                <button type="submit" class="w-full py-1.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700">Tolak Permanen</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Dokumen --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 mb-3 border-b border-gray-100 pb-2">Dokumen Terlampir</h3>
                <div class="space-y-3">
                    @if($peminjaman->file_sik)
                    <a href="{{ Storage::url($peminjaman->file_sik) }}" target="_blank" class="flex items-center p-2 rounded-lg bg-gray-50 hover:bg-gray-100 text-sm text-gray-700 border border-gray-200">
                        <i data-feather="file-text" class="w-4 h-4 mr-2 text-brand-600"></i> SIK
                    </a>
                    @endif
                    @if($peminjaman->file_proposal)
                    <a href="{{ Storage::url($peminjaman->file_proposal) }}" target="_blank" class="flex items-center p-2 rounded-lg bg-gray-50 hover:bg-gray-100 text-sm text-gray-700 border border-gray-200">
                        <i data-feather="book-open" class="w-4 h-4 mr-2 text-brand-600"></i> Proposal/TOR
                    </a>
                    @endif
                    @if($peminjaman->file_persetujuan_fasilitas)
                    <a href="{{ Storage::url($peminjaman->file_persetujuan_fasilitas) }}" target="_blank" class="flex items-center p-2 rounded-lg bg-gray-50 hover:bg-gray-100 text-sm text-gray-700 border border-gray-200">
                        <i data-feather="check-square" class="w-4 h-4 mr-2 text-brand-600"></i> Persetujuan Fasilitas
                    </a>
                    @endif
                    
                    @if($peminjaman->status === 'disetujui')
                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <a href="{{ route('peminjaman.cetak', $peminjaman) }}" target="_blank" class="flex items-center justify-center p-2.5 rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 text-sm font-bold text-white shadow-md shadow-brand-200 transition-colors">
                            <i data-feather="printer" class="w-4 h-4 mr-2"></i>
                            {{ $peminjaman->jenis_peminjaman === 'luar_fakultas' ? 'Cetak Surat Pengantar' : 'Cetak Surat Balasan' }}
                        </a>
                    </div>
                    @endif

                    @if(!$peminjaman->file_sik && !$peminjaman->file_proposal && !$peminjaman->file_persetujuan_fasilitas)
                    <p class="text-xs text-gray-500 italic">Tidak ada dokumen.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Chat/Revisi Thread --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 h-full flex flex-col max-h-[700px]">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="message-square" class="w-5 h-5 mr-2 text-brand-600"></i> Ruang Diskusi / Revisi
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Gunakan fitur ini untuk membahas kekurangan berkas atau perbaikan.</p>
                </div>
                
                {{-- Chat Messages --}}
                <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-gray-50/50">
                    @forelse($peminjaman->chats as $chat)
                        @php
                            $isMe = $chat->user_id === auth()->id();
                            $isMahasiswa = $chat->user->role->name === 'Mahasiswa';
                        @endphp
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%]">
                                <div class="text-[10px] text-gray-400 mb-1 {{ $isMe ? 'text-right' : '' }}">
                                    {{ $chat->user->name }} • {{ $chat->created_at->format('d M H:i') }}
                                </div>
                                <div class="p-3 rounded-2xl text-sm shadow-sm
                                    {{ $isMe ? 'bg-brand-600 text-white rounded-tr-sm' : ($isMahasiswa ? 'bg-white border border-gray-200 text-gray-800 rounded-tl-sm' : 'bg-orange-50 border border-orange-100 text-orange-900 rounded-tl-sm') }}">
                                    
                                    @if($chat->message)
                                        <p class="whitespace-pre-wrap">{{ $chat->message }}</p>
                                    @endif
                                    
                                    @if($chat->attachment_path)
                                        <a href="{{ Storage::url($chat->attachment_path) }}" target="_blank" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white/20 hover:bg-white/30 border {{ $isMe ? 'border-white/30 text-white' : 'border-gray-200 text-brand-700 bg-brand-50' }} transition-colors">
                                            <i data-feather="paperclip" class="w-3 h-3 mr-1"></i> Lampiran File Revisi
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex items-center justify-center text-gray-400 text-sm">
                            Belum ada pesan.
                        </div>
                    @endforelse
                </div>

                {{-- Chat Input --}}
                <div class="p-4 border-t border-gray-100 bg-white rounded-b-2xl">
                    <form action="{{ route('peminjaman.chat.store', $peminjaman) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-end gap-3">
                            <div class="flex-1">
                                <textarea name="message" rows="2" class="w-full text-sm border-gray-300 rounded-xl focus:ring-brand-500 focus:border-brand-500 bg-gray-50" placeholder="Ketik pesan Anda..."></textarea>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="cursor-pointer inline-flex items-center justify-center p-2.5 text-gray-500 hover:text-brand-600 bg-gray-50 hover:bg-brand-50 border border-gray-200 rounded-xl transition-colors" title="Lampirkan File">
                                    <i data-feather="paperclip" class="w-5 h-5"></i>
                                    <input type="file" name="attachment" class="hidden" accept="application/pdf">
                                </label>
                                <button type="submit" class="p-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl shadow-md transition-colors">
                                    <i data-feather="send" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        @error('attachment')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
