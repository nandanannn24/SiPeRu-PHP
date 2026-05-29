@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
<div class="max-w-4xl mx-auto" x-data="peminjamanForm()">

    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-brand-600 transition-colors">
            <i data-feather="arrow-left" class="w-4 h-4 mr-1.5"></i> Kembali ke Dashboard
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-brand-700 to-brand-800 px-8 py-6 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full"></div>
            <div class="absolute -left-8 -bottom-8 w-32 h-32 bg-white/5 rounded-full"></div>

            <h2 class="text-2xl font-bold text-white flex items-center relative z-10">
                <i data-feather="edit-3" class="w-6 h-6 mr-3"></i> Formulir Pengajuan Ruangan
            </h2>
            <p class="text-brand-200 text-sm mt-2 relative z-10">Pengajuan harus dilakukan minimal H-14 sebelum kegiatan diselenggarakan.</p>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="mx-8 mt-6 p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="flex items-start">
                <i data-feather="alert-triangle" class="w-5 h-5 text-red-500 mr-3 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-sm font-semibold text-red-800 mb-1">Terdapat kesalahan:</p>
                    <ul class="text-sm text-red-700 space-y-0.5 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('peminjaman.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf

            {{-- Section 1: Pemilihan Ruangan --}}
            <div class="space-y-5">
                <h3 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-2 flex items-center">
                    <span class="w-7 h-7 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold mr-2.5">1</span>
                    Detail Ruangan
                </h3>
                
                {{-- Jenis Peminjaman --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Ruangan</label>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors w-full"
                               :class="{'bg-brand-50 border-brand-300': jenisPilihan === 'aula'}">
                            <input type="radio" x-model="jenisPilihan" value="aula" @change="selectedRuangan = ''" class="w-4 h-4 text-brand-600 focus:ring-brand-500 border-gray-300">
                            <span class="ml-3 text-sm font-medium text-gray-800">Peminjaman Aula</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors w-full"
                               :class="{'bg-brand-50 border-brand-300': jenisPilihan === 'kelas'}">
                            <input type="radio" x-model="jenisPilihan" value="kelas" @change="selectedRuangan = ''" class="w-4 h-4 text-brand-600 focus:ring-brand-500 border-gray-300">
                            <span class="ml-3 text-sm font-medium text-gray-800">Peminjaman Ruang Kelas</span>
                        </label>
                    </div>
                </div>

                {{-- Pilih Lokasi --}}
                <div>
                    <label for="ruangan_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Lokasi / Ruangan</label>
                    <select id="ruangan_id" name="ruangan_id" x-model="selectedRuangan"
                        class="block w-full pl-3 pr-10 py-3 text-sm border border-gray-200 bg-gray-50 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:bg-white transition-all @error('ruangan_id') border-red-300 ring-red-200 @enderror">
                        <option value="">-- Silakan Pilih Lokasi Ruangan --</option>
                        
                        <template x-for="(group, gedungName) in groupedRuangans" :key="gedungName">
                            <optgroup :label="gedungName">
                                <template x-for="r in group" :key="r.id">
                                    <option :value="r.id" x-text="r.name + (r.fakultas ? ' (' + r.fakultas.name + ')' : ' (Fasilitas Umum)')"></option>
                                </template>
                            </optgroup>
                        </template>
                    </select>
                    @error('ruangan_id')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center"><i data-feather="alert-circle" class="w-3 h-3 mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Section 2: Waktu Pelaksanaan --}}
            <div class="space-y-4 pt-2 border-t border-gray-100">
                <h3 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-2 flex items-center">
                    <span class="w-7 h-7 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold mr-2.5">2</span>
                    Waktu Pelaksanaan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="waktu_mulai" class="block text-sm font-semibold text-gray-700 mb-1.5">Mulai Acara</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-feather="calendar" class="w-4 h-4 text-gray-400"></i>
                            </div>
                            <input type="datetime-local" id="waktu_mulai" name="waktu_mulai"
                                value="{{ old('waktu_mulai') }}"
                                min="{{ now()->addDays(14)->format('Y-m-d\TH:i') }}"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl shadow-sm bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:bg-white transition-all @error('waktu_mulai') border-red-300 @enderror">
                        </div>
                        @error('waktu_mulai')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center"><i data-feather="alert-circle" class="w-3 h-3 mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="waktu_selesai" class="block text-sm font-semibold text-gray-700 mb-1.5">Selesai Acara</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-feather="calendar" class="w-4 h-4 text-gray-400"></i>
                            </div>
                            <input type="datetime-local" id="waktu_selesai" name="waktu_selesai"
                                value="{{ old('waktu_selesai') }}"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl shadow-sm bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:bg-white transition-all @error('waktu_selesai') border-red-300 @enderror">
                        </div>
                        @error('waktu_selesai')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center"><i data-feather="alert-circle" class="w-3 h-3 mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 3: Deskripsi Kegiatan --}}
            <div class="space-y-4 pt-2 border-t border-gray-100">
                <h3 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-2 flex items-center">
                    <span class="w-7 h-7 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold mr-2.5">3</span>
                    Deskripsi Kegiatan
                </h3>
                <div>
                    <label for="keperluan" class="block text-sm font-semibold text-gray-700 mb-1.5">Tujuan / Keperluan Peminjaman</label>
                    <textarea id="keperluan" name="keperluan" rows="4"
                        class="block w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:bg-white transition-all @error('keperluan') border-red-300 @enderror"
                        placeholder="Tuliskan keterangan detail kegiatan...">{{ old('keperluan') }}</textarea>
                    @error('keperluan')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center"><i data-feather="alert-circle" class="w-3 h-3 mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Section 4: File Upload --}}
            <div class="pt-2 border-t border-gray-100 space-y-4">
                <h3 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-2 flex items-center">
                    <span class="w-7 h-7 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold mr-2.5">4</span>
                    <i data-feather="folder" class="w-4 h-4 mr-2 text-brand-600"></i>
                    Kelengkapan Berkas (Wajib)
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 bg-gray-50 p-6 rounded-xl border border-gray-200">
                    @php
                        $fileFields = [
                            ['name' => 'file_sik', 'label' => 'Surat Izin Kegiatan (SIK)', 'icon' => 'file-text'],
                            ['name' => 'file_proposal', 'label' => 'Proposal / TOR', 'icon' => 'book-open'],
                            ['name' => 'file_persetujuan_fasilitas', 'label' => 'Persetujuan CS & Satpam', 'icon' => 'check-square'],
                        ];
                    @endphp

                    @foreach($fileFields as $field)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:border-brand-200 transition-colors">
                        <label class="block text-sm font-bold text-gray-800 mb-1 flex items-center">
                            <i data-feather="{{ $field['icon'] }}" class="w-4 h-4 mr-1.5 text-brand-600"></i>
                            {{ $field['label'] }}
                        </label>
                        <p class="text-xs text-gray-400 mb-3">Format PDF, maksimal 5MB</p>
                        <input type="file" name="{{ $field['name'] }}" accept="application/pdf"
                            class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer transition-colors">
                        @error($field['name'])
                            <p class="mt-1.5 text-xs text-red-600 flex items-center"><i data-feather="alert-circle" class="w-3 h-3 mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-6 flex flex-col sm:flex-row justify-end items-center border-t border-gray-100 gap-3">
                <a href="{{ route('dashboard') }}"
                   class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors shadow-sm text-center">
                    Batal
                </a>
                <button type="submit"
                    class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3 border border-transparent rounded-xl shadow-lg shadow-brand-200 text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all duration-200">
                    Kirim Pengajuan <i data-feather="send" class="w-4 h-4 ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function peminjamanForm() {
        return {
            userFakultasId: @json($userFakultasId),
            allRuangans: @json($ruangans),
            jenisPilihan: 'kelas', // default
            selectedRuangan: '{{ old("ruangan_id", "") }}',
            isLuarFakultas: false,

            init() {
                // Initial load
                if (this.selectedRuangan) {
                    const r = this.allRuangans.find(x => x.id == this.selectedRuangan);
                    if (r) {
                        this.jenisPilihan = r.jenis;
                    }
                }

                // Watch for changes in selectedRuangan
                this.$watch('selectedRuangan', (value) => {
                    // if (!value) return; // not strictly needed
                });
            },

            get groupedRuangans() {
                const filtered = this.allRuangans.filter(r => r.jenis === this.jenisPilihan);
                const grouped = {};
                filtered.forEach(r => {
                    const gedung = r.gedung || 'Fasilitas Lainnya';
                    if (!grouped[gedung]) grouped[gedung] = [];
                    grouped[gedung].push(r);
                });
                return grouped;
            }
        }
    }
</script>
@endpush
