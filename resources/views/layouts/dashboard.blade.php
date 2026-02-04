<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Dashboard') — LMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-white text-slate-900">
    <!-- Header -->
    <header class="border-b border-slate-200 sticky top-0 z-40 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-bold text-lg hover:text-slate-700">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" class="text-slate-900">
                    <rect x="3" y="4" width="7" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="14" y="4" width="7" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M3 14h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                LMS
            </a>

            <!-- Navigation -->
            <nav class="hidden md:flex items-center gap-6 ml-8">
                @if(auth()->user()?->role === 'siswa')
                    <a href="{{ route('siswa.materials') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">Materi</a>
                    <a href="{{ route('siswa.chat') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">Chat</a>
                    <a href="{{ route('siswa.reports') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">Laporan</a>
                @elseif(auth()->user()?->role === 'guru')
                    <a href="{{ route('guru.materials') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">Materi</a>
                    <a href="{{ route('guru.chat') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">Chat</a>
                    <a href="{{ route('guru.reports') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">Laporan</a>
                @elseif(auth()->user()?->role === 'admin')
                    <a href="{{ route('admin.users') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">Pengguna</a>
                    <a href="{{ route('admin.reports') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">Laporan</a>
                @endif
            </nav>

            <!-- User Menu -->
            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-600 hidden sm:block">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-slate-600 hover:text-slate-900 transition">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 mt-20 py-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center text-sm text-slate-500">
            &copy; 2026 LMS — Learning Management System
        </div>
    </footer>
</body>
</html>
