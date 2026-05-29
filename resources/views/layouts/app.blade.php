<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SiPeRu - Sistem Peminjaman Ruangan UPN Veteran Jawa Timur">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SiPeRu') - Sistem Peminjaman Ruangan</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Feather Icons --}}
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Smooth scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }

        /* Subtle gradient on body */
        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 50%, #ecfdf5 100%);
            min-height: 100vh;
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased text-gray-800 flex flex-col min-h-screen" x-data="{ mobileMenuOpen: false }">

    {{-- ── Navbar ────────────────────────────────────────── --}}
    <nav class="bg-white/80 backdrop-blur-xl border-b border-brand-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                {{-- Logo & Brand --}}
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <img src="{{ asset('logo.webp') }}" alt="Logo UPN" class="h-9 w-9 rounded-lg shadow-sm object-cover">
                        <div class="hidden sm:block">
                            <span class="text-lg font-bold text-brand-800 tracking-tight group-hover:text-brand-600 transition-colors">SiPeRu</span>
                            <span class="text-xs text-gray-400 block leading-none -mt-0.5">UPNVJT</span>
                        </div>
                    </a>
                </div>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center space-x-1">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200
                            {{ request()->routeIs('dashboard') ? 'text-brand-700 bg-brand-50' : 'text-gray-600 hover:text-brand-700 hover:bg-brand-50/50' }}">
                            <i data-feather="layout" class="w-4 h-4 mr-1.5"></i> Dashboard
                        </a>
                        <a href="{{ route('peminjaman.create') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200
                            {{ request()->routeIs('peminjaman.create') ? 'text-brand-700 bg-brand-50' : 'text-gray-600 hover:text-brand-700 hover:bg-brand-50/50' }}">
                            <i data-feather="plus-circle" class="w-4 h-4 mr-1.5"></i> Ajukan
                        </a>
                        <a href="{{ route('peminjaman.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200
                            {{ request()->routeIs('peminjaman.index') ? 'text-brand-700 bg-brand-50' : 'text-gray-600 hover:text-brand-700 hover:bg-brand-50/50' }}">
                            <i data-feather="list" class="w-4 h-4 mr-1.5"></i> Riwayat
                        </a>

                        <div class="w-px h-8 bg-gray-200 mx-2"></div>

                        {{-- User Info --}}
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 group text-left hover:bg-gray-50 px-2 py-1 rounded-lg transition-colors">
                                <div class="text-right hidden lg:block">
                                    <p class="text-sm font-semibold text-gray-800 leading-tight group-hover:text-brand-600">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-400">{{ Auth::user()->role?->name ?? 'User' }}</p>
                                </div>
                                <div class="h-9 w-9 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center text-sm font-bold shadow-md shadow-brand-200">
                                    {{ Auth::user()->initials }}
                                </div>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200" title="Logout">
                                    <i data-feather="log-out" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>

                {{-- Mobile Menu Button --}}
                <div class="flex items-center md:hidden">
                    @auth
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:text-brand-700 hover:bg-brand-50 transition-colors">
                        <i x-show="!mobileMenuOpen" data-feather="menu" class="w-5 h-5"></i>
                        <i x-show="mobileMenuOpen" x-cloak data-feather="x" class="w-5 h-5"></i>
                    </button>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        @auth
        <div x-show="mobileMenuOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t border-gray-100 bg-white/95 backdrop-blur-xl">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-3 py-3 bg-brand-50/50 hover:bg-brand-50 rounded-lg mb-2 transition-colors">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center text-sm font-bold">
                        {{ Auth::user()->initials }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->role?->name ?? 'User' }}</p>
                    </div>
                </a>

                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'text-brand-700 bg-brand-50' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i data-feather="layout" class="w-4 h-4 mr-3"></i> Dashboard
                </a>
                <a href="{{ route('peminjaman.create') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('peminjaman.create') ? 'text-brand-700 bg-brand-50' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i data-feather="plus-circle" class="w-4 h-4 mr-3"></i> Ajukan Peminjaman
                </a>
                <a href="{{ route('peminjaman.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('peminjaman.index') ? 'text-brand-700 bg-brand-50' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i data-feather="list" class="w-4 h-4 mr-3"></i> Riwayat
                </a>

                <div class="border-t border-gray-100 pt-2 mt-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg">
                            <i data-feather="log-out" class="w-4 h-4 mr-3"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </nav>

    {{-- ── Flash Messages ────────────────────────────────── --}}
    @if(session('success') || session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)">
        @if(session('success'))
        <div x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
             class="flex items-center p-4 rounded-xl bg-brand-50 border border-brand-200 text-brand-800 shadow-sm">
            <i data-feather="check-circle" class="w-5 h-5 mr-3 text-brand-600 flex-shrink-0"></i>
            <p class="text-sm font-medium flex-1">{{ session('success') }}</p>
            <button @click="show = false" class="ml-3 text-brand-400 hover:text-brand-600"><i data-feather="x" class="w-4 h-4"></i></button>
        </div>
        @endif
        @if(session('error'))
        <div x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
             class="flex items-center p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 shadow-sm">
            <i data-feather="alert-circle" class="w-5 h-5 mr-3 text-red-600 flex-shrink-0"></i>
            <p class="text-sm font-medium flex-1">{{ session('error') }}</p>
            <button @click="show = false" class="ml-3 text-red-400 hover:text-red-600"><i data-feather="x" class="w-4 h-4"></i></button>
        </div>
        @endif
    </div>
    @endif

    {{-- ── Main Content ──────────────────────────────────── --}}
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    {{-- ── Footer ────────────────────────────────────────── --}}
    <footer class="bg-white/60 backdrop-blur border-t border-gray-100 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-3">
            <div class="flex items-center space-x-2 text-sm text-gray-400">
                <img src="{{ asset('logo.webp') }}" alt="Logo" class="h-5 w-5 rounded object-cover opacity-50">
                <span>&copy; {{ date('Y') }} SiPeRu &mdash; UPN Veteran Jawa Timur</span>
            </div>
            <div class="flex items-center space-x-4 text-gray-400">
                <a href="#" class="hover:text-brand-600 transition-colors" title="Bantuan">
                    <i data-feather="help-circle" class="w-4 h-4"></i>
                </a>
                <a href="#" class="hover:text-brand-600 transition-colors" title="Informasi">
                    <i data-feather="info" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => feather.replace());
        document.addEventListener('alpine:initialized', () => {
            setTimeout(() => feather.replace(), 100);
        });
    </script>
    @stack('scripts')
</body>
</html>
