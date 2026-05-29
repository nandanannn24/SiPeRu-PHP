@extends('layouts.app')

@section('title', 'Profil & Tanda Tangan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
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

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 flex items-center mb-6">
            <i data-feather="user" class="w-6 h-6 mr-2 text-brand-600"></i> Profil Pengguna
        </h2>

        <div class="space-y-4 mb-8">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Nama</label>
                <p class="text-gray-900 font-medium">{{ $user->name }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Email</label>
                <p class="text-gray-900 font-medium">{{ $user->email }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Jabatan / Role</label>
                <p class="text-gray-900 font-medium">{{ $user->role->name }}</p>
            </div>
        </div>

        @if(in_array($user->role->name, ['Kaprodi', 'Dekan 3', 'TU Fakultas']))
        <hr class="border-gray-100 my-6">

        <h3 class="text-lg font-bold text-gray-900 flex items-center mb-4">
            <i data-feather="edit-3" class="w-5 h-5 mr-2 text-brand-600"></i> Tanda Tangan Digital
        </h3>
        <p class="text-sm text-gray-500 mb-6">Unggah foto tanda tangan Anda (PNG transparan) untuk di-stamp otomatis ke dalam PDF Surat Pengantar.</p>

        @if($user->signature_path)
        <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200 inline-block">
            <p class="text-xs font-semibold text-gray-500 mb-2">Tanda Tangan Saat Ini:</p>
            <img src="{{ Storage::url($user->signature_path) }}" alt="Tanda Tangan" class="h-24 object-contain">
        </div>
        @endif

        <form action="{{ route('profile.signature.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Upload Baru (PNG, Max 2MB)</label>
                <input type="file" name="signature" accept="image/png, image/jpeg" required
                    class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer transition-colors border border-gray-200 rounded-xl">
                @error('signature')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">
                Simpan Tanda Tangan
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
