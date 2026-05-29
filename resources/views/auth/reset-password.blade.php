@extends('layouts.guest')

@section('title', 'Atur Ulang Password')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl shadow-2xl overflow-hidden min-h-[540px]">

    {{-- Left Panel: Branding --}}
    <div class="hidden lg:flex flex-col justify-center items-center bg-gradient-to-br from-brand-700 via-brand-800 to-brand-900 p-12 relative overflow-hidden">
        {{-- Decorative circles --}}
        <div class="absolute -top-20 -left-20 w-60 h-60 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-16 -right-16 w-48 h-48 bg-white/5 rounded-full"></div>
        <div class="absolute top-1/2 right-0 w-32 h-32 bg-brand-500/10 rounded-full blur-xl"></div>

        <div class="relative z-10 text-center space-y-6">
            <img src="{{ asset('logo.webp') }}" alt="Logo UPN Veteran Jawa Timur" class="h-24 w-24 mx-auto rounded-2xl shadow-xl object-cover">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">SiPeRu</h1>
                <p class="text-brand-200 text-sm mt-1 font-medium">Sistem Peminjaman Ruangan</p>
            </div>
            <div class="w-16 h-0.5 bg-brand-400/40 mx-auto rounded-full"></div>
            <p class="text-brand-200/80 text-sm max-w-xs leading-relaxed">
                Silakan buat kata sandi baru Anda yang kuat dan aman.
            </p>
        </div>
    </div>

    {{-- Right Panel: Reset Password Form --}}
    <div class="flex flex-col justify-center p-8 sm:p-12">
        {{-- Mobile logo --}}
        <div class="lg:hidden text-center mb-8">
            <img src="{{ asset('logo.webp') }}" alt="Logo UPN" class="h-16 w-16 mx-auto rounded-xl shadow-md object-cover mb-3">
            <h1 class="text-xl font-bold text-brand-800">SiPeRu</h1>
            <p class="text-xs text-gray-400">Sistem Peminjaman Ruangan</p>
        </div>

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Atur Ulang Kata Sandi</h2>
            <p class="text-gray-500 text-sm mt-2 leading-relaxed">Masukkan email dan kata sandi baru Anda di bawah ini.</p>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="flex items-start">
                <i data-feather="alert-triangle" class="w-5 h-5 text-red-500 mr-3 flex-shrink-0 mt-0.5"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-700">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf

            {{-- Token (Hidden) --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Terdaftar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-feather="mail" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly
                        class="block w-full pl-10 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm text-gray-500 focus:outline-none cursor-not-allowed">
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi Baru</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-feather="lock" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="password" id="password" name="password" required autofocus
                        class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:bg-white transition-all"
                        placeholder="Minimal 8 karakter">
                </div>
            </div>

            {{-- Password Confirmation --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i data-feather="lock" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:bg-white transition-all"
                        placeholder="Ketik ulang kata sandi">
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full flex justify-center items-center py-3 px-4 mt-2 bg-gradient-to-r from-brand-600 to-brand-700 text-white font-semibold rounded-xl shadow-lg shadow-brand-200 hover:shadow-xl hover:from-brand-700 hover:to-brand-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all duration-200 text-sm">
                <i data-feather="save" class="w-4 h-4 mr-2"></i>
                Simpan Kata Sandi Baru
            </button>
        </form>
    </div>
</div>
@endsection
