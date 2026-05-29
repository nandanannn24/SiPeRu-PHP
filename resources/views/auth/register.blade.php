@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl shadow-2xl overflow-hidden min-h-[580px]">

    {{-- Left Panel: Branding --}}
    <div class="hidden lg:flex flex-col justify-center items-center bg-gradient-to-br from-brand-700 via-brand-800 to-brand-900 p-12 relative overflow-hidden">
        <div class="absolute -top-20 -left-20 w-60 h-60 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-16 -right-16 w-48 h-48 bg-white/5 rounded-full"></div>

        <div class="relative z-10 text-center space-y-6">
            <img src="{{ asset('logo.webp') }}" alt="Logo UPN Veteran Jawa Timur" class="h-24 w-24 mx-auto rounded-2xl shadow-xl object-cover">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">SiPeRu</h1>
                <p class="text-brand-200 text-sm mt-1 font-medium">Sistem Peminjaman Ruangan</p>
            </div>
            <div class="w-16 h-0.5 bg-brand-400/40 mx-auto rounded-full"></div>

            {{-- NPM Info Box --}}
            <div class="bg-white/10 backdrop-blur rounded-xl p-5 text-left max-w-xs mx-auto border border-white/10">
                <div class="flex items-center mb-3">
                    <i data-feather="info" class="w-4 h-4 text-brand-300 mr-2"></i>
                    <span class="text-sm font-semibold text-white">Deteksi Otomatis NPM</span>
                </div>
                <p class="text-xs text-brand-200/90 leading-relaxed">
                    Gunakan email mahasiswa Anda (contoh: <span class="font-mono text-brand-300">24081010037@student.upnjatim.ac.id</span>). Sistem akan otomatis mendeteksi angkatan, fakultas, dan program studi dari NPM.
                </p>
                <div class="mt-3 text-xs text-brand-300/70 space-y-1">
                    <div class="flex items-center"><i data-feather="hash" class="w-3 h-3 mr-1.5"></i> 24 = Angkatan 2024</div>
                    <div class="flex items-center"><i data-feather="home" class="w-3 h-3 mr-1.5"></i> 08 = Fasilkom</div>
                    <div class="flex items-center"><i data-feather="book" class="w-3 h-3 mr-1.5"></i> 010 = Teknik Informatika</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Panel: Register Form --}}
    <div class="flex flex-col justify-center p-8 sm:p-12">
        {{-- Mobile logo --}}
        <div class="lg:hidden text-center mb-6">
            <img src="{{ asset('logo.webp') }}" alt="Logo UPN" class="h-14 w-14 mx-auto rounded-xl shadow-md object-cover mb-2">
            <h1 class="text-lg font-bold text-brand-800">SiPeRu</h1>
        </div>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Buat Akun Baru</h2>
            <p class="text-gray-500 text-sm mt-1">Daftarkan diri Anda untuk mengajukan peminjaman ruangan</p>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="flex items-start">
                <i data-feather="alert-triangle" class="w-5 h-5 text-red-500 mr-3 flex-shrink-0 mt-0.5"></i>
                <ul class="text-sm text-red-700 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            {{-- Nama Lengkap --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-feather="user" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:bg-white transition-all"
                        placeholder="Masukkan nama lengkap">
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Mahasiswa</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-feather="mail" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:bg-white transition-all"
                        placeholder="npm@student.upnjatim.ac.id">
                </div>
                <p class="mt-1.5 text-xs text-gray-400 flex items-center">
                    <i data-feather="info" class="w-3 h-3 mr-1"></i>
                    NPM, fakultas, dan prodi akan terdeteksi otomatis dari email
                </p>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-feather="lock" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="password" id="password" name="password" required
                        class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:bg-white transition-all"
                        placeholder="Minimal 8 karakter">
                </div>
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-feather="lock" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:bg-white transition-all"
                        placeholder="Ulangi password">
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-brand-600 to-brand-700 text-white font-semibold rounded-xl shadow-lg shadow-brand-200 hover:shadow-xl hover:from-brand-700 hover:to-brand-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all duration-200 text-sm mt-2">
                <i data-feather="user-plus" class="w-4 h-4 mr-2"></i>
                Daftar Sekarang
            </button>
        </form>

        {{-- Login Link --}}
        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
