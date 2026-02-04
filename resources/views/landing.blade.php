<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS - Learning Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-900">
    <!-- Page Loader (shown during refresh / navigation) -->
    <div id="pageLoader" class="fixed inset-0 z-50 flex items-center justify-center bg-white/60 backdrop-blur-sm transition-opacity duration-300 opacity-100">
      <div class="flex flex-col items-center gap-3">
        <svg class="w-10 h-10 text-slate-900 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle>
          <path class="opacity-75" d="M4 12a8 8 0 018-8" stroke-width="4" stroke-linecap="round"></path>
        </svg>
        <p class="text-sm text-slate-700">Memuat…</p>
      </div>
    </div>
    <!-- Navbar (glassy) -->
    <nav id="mainNav" class="fixed top-0 w-full bg-white/50 backdrop-blur-sm border-b border-slate-100/50 z-50 shadow-sm transition-all duration-200">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="text-slate-900">
                    <rect x="3" y="4" width="7" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="14" y="4" width="7" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M3 14h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span class="text-xl font-bold text-slate-900">LMS</span>
            </a>

            <div class="flex items-center gap-3">
                @guest
                    <button id="openLogin" class="text-sm text-slate-700 hover:text-slate-900 font-medium transition-colors">Masuk</button>
                    <button id="openRegister" class="text-sm px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-all duration-200 hover:shadow-lg active:scale-95">Daftar</button>
                @else
                    <a href="{{ route('dashboard') }}" class="text-sm text-slate-700 hover:text-slate-900 transition-colors">Dashboard</a>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <div class="space-y-4">
                        <h1 class="text-5xl lg:text-6xl font-bold text-slate-900 leading-tight">
                            Platform Pembelajaran<br><span class="bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">Modern & Terpercaya</span>
                        </h1>
                        <p class="text-xl text-slate-600 max-w-lg leading-relaxed">
                            Kelola pembelajaran dengan mudah. Guru upload materi, siswa belajar & lapor, semua terhubung dalam satu platform yang aman dan intuitif.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        @guest
                            <button id="openRegister" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800 transition-all duration-200 hover:shadow-lg hover:shadow-slate-900/20 active:scale-95 text-lg">Mulai Gratis</button>
                            <button id="openLogin" class="px-6 py-3 border-2 border-slate-200 text-slate-900 rounded-xl font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 active:scale-95 text-lg">Masuk</button>
                        @else
                            <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800 transition-all duration-200 hover:shadow-lg text-lg text-center">Buka Dashboard</a>
                        @endguest
                    </div>
                </div>

                <!-- Hero Visual -->
                <div class="hidden lg:block">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-purple-400/20 rounded-3xl blur-3xl"></div>
                        <div class="relative bg-gradient-to-br from-slate-50 to-slate-100 rounded-3xl p-12 border border-slate-200">
                            <div class="space-y-6">
                                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-slate-900">Materi Berkualitas</h3>
                                            <p class="text-sm text-slate-600">Guru upload & kelola materi</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-slate-900">Chat Langsung</h3>
                                            <p class="text-sm text-slate-600">Terhubung saat jam kerja</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-slate-900">Laporan Terstruktur</h3>
                                            <p class="text-sm text-slate-600">Submit & track progress</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-6 lg:px-8 bg-gradient-to-b from-white via-slate-50 to-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold text-slate-900 mb-4">Fitur Unggulan</h2>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">Semua yang Anda butuhkan untuk pembelajaran yang efektif</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature Card 1 -->
                <div class="group bg-white rounded-2xl p-8 border border-slate-200 hover:border-slate-300 hover:shadow-lg transition-all duration-300 cursor-pointer" onclick="openFeatureModal(this, 'Manajemen Materi', 'Guru dapat dengan mudah upload, edit, dan mengelola materi pembelajaran. Siswa dapat mengakses dan mendownload kapan saja. Semua materi tersimpan aman di cloud dan dapat diakses kapan saja.', 'blue')">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Manajemen Materi</h3>
                    <p class="text-slate-600 leading-relaxed">Guru dapat dengan mudah upload, edit, dan mengelola materi pembelajaran. Siswa dapat mengakses dan mendownload kapan saja.</p>
                </div>

                <!-- Feature Card 2 -->
                <div class="group bg-white rounded-2xl p-8 border border-slate-200 hover:border-slate-300 hover:shadow-lg transition-all duration-300 cursor-pointer" onclick="openFeatureModal(this, 'Chat Real-time', 'Komunikasi langsung antara siswa dan guru. Tersedia sesuai jam kerja untuk menjaga fokus pembelajaran. Pesan tersimpan untuk referensi kemudian.', 'green')">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Chat Real-time</h3>
                    <p class="text-slate-600 leading-relaxed">Komunikasi langsung antara siswa dan guru. Tersedia sesuai jam kerja untuk menjaga fokus pembelajaran.</p>
                </div>

                <!-- Feature Card 3 -->
                <div class="group bg-white rounded-2xl p-8 border border-slate-200 hover:border-slate-300 hover:shadow-lg transition-all duration-300 cursor-pointer" onclick="openFeatureModal(this, 'Sistem Laporan', 'Siswa laporkan masalah atau progress, guru dan admin pantau semua laporan dengan tracking yang jelas. Update status laporan dari open hingga solved.', 'purple')">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 12a5 5 0 1110 0 5 5 0 01-10 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Sistem Laporan</h3>
                    <p class="text-slate-600 leading-relaxed">Siswa laporkan masalah atau progress, guru dan admin pantau semua laporan dengan tracking yang jelas.</p>
                </div>

                <!-- Feature Card 4 -->
                <div class="group bg-white rounded-2xl p-8 border border-slate-200 hover:border-slate-300 hover:shadow-lg transition-all duration-300 cursor-pointer" onclick="openFeatureModal(this, 'Akses Cepat', 'Interface yang ringan dan responsif. Akses dari desktop maupun mobile tanpa hambatan. Loading cepat dan performa optimal.', 'orange')">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-100 to-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Akses Cepat</h3>
                    <p class="text-slate-600 leading-relaxed">Interface yang ringan dan responsif. Akses dari desktop maupun mobile tanpa hambatan.</p>
                </div>

                <!-- Feature Card 5 -->
                <div class="group bg-white rounded-2xl p-8 border border-slate-200 hover:border-slate-300 hover:shadow-lg transition-all duration-300 cursor-pointer" onclick="openFeatureModal(this, 'Aman & Terpercaya', 'Data terenkripsi dan aman. Sistem auth yang ketat menjamin privasi semua pengguna. Backup otomatis setiap hari.', 'red')">
                    <div class="w-14 h-14 bg-gradient-to-br from-red-100 to-red-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Aman & Terpercaya</h3>
                    <p class="text-slate-600 leading-relaxed">Data terenkripsi dan aman. Sistem auth yang ketat menjamin privasi semua pengguna.</p>
                </div>

                <!-- Feature Card 6 -->
                <div class="group bg-white rounded-2xl p-8 border border-slate-200 hover:border-slate-300 hover:shadow-lg transition-all duration-300 cursor-pointer" onclick="openFeatureModal(this, 'Dashboard Admin', 'Panel kontrol lengkap untuk admin kelola pengguna, materi, dan laporan dengan mudah. Analytics dan insights pembelajaran.', 'indigo')">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Dashboard Admin</h3>
                    <p class="text-slate-600 leading-relaxed">Panel kontrol lengkap untuk admin kelola pengguna, materi, dan laporan dengan mudah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-12 lg:p-16 text-center text-white border border-slate-700">
                <h2 class="text-4xl lg:text-5xl font-bold mb-6">Siap untuk Transformasi Digital?</h2>
                <p class="text-xl text-slate-200 mb-10 max-w-2xl mx-auto">
                    Bergabunglah dengan ribuan guru dan siswa yang telah merasakan manfaat platform LMS kami. Mulai perjalanan pembelajaran yang lebih efektif hari ini.
                </p>
                @guest
                    <button id="openRegister" class="px-8 py-4 bg-white text-slate-900 rounded-xl font-bold text-lg hover:bg-slate-100 transition-all duration-200 hover:shadow-xl hover:shadow-white/20 active:scale-95 inline-block">
                        Daftar Sekarang
                    </button>
                @else
                    <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-white text-slate-900 rounded-xl font-bold text-lg hover:bg-slate-100 transition-all duration-200 hover:shadow-xl inline-block">
                        Buka Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-100 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
            <div class="flex flex-col lg:flex-row gap-12 mb-8 items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="text-slate-900">
                        <rect x="3" y="4" width="7" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="14" y="4" width="7" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M3 14h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span class="text-lg font-bold text-slate-900">LMS</span>
                </div>

                <div class="flex flex-wrap gap-8 justify-center lg:justify-start">
                    <div class="text-center lg:text-left">
                        <h4 class="font-semibold text-slate-900 mb-3">Produk</h4>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Fitur</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Harga</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Demo</a></li>
                        </ul>
                    </div>
                    <div class="text-center lg:text-left">
                        <h4 class="font-semibold text-slate-900 mb-3">Perusahaan</h4>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Tentang</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Blog</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Karir</a></li>
                        </ul>
                    </div>
                    <div class="text-center lg:text-left">
                        <h4 class="font-semibold text-slate-900 mb-3">Legal</h4>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Privasi</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Syarat</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Kontak</a></li>
                        </ul>
                    </div>
                    <div class="text-center lg:text-left">
                        <h4 class="font-semibold text-slate-900 mb-3">Ikuti Kami</h4>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Twitter</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">LinkedIn</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Instagram</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200 pt-8 flex flex-col lg:flex-row justify-between items-center gap-4 text-sm text-slate-600">
                <p>&copy; 2026 LMS. Semua hak dilindungi.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-slate-900 transition-colors">Privasi</a>
                    <a href="#" class="hover:text-slate-900 transition-colors">Syarat</a>
                    <a href="#" class="hover:text-slate-900 transition-colors">Status</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div id="loginModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-40 p-4 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl relative transform scale-95 opacity-0 transition-all duration-200"
             role="dialog" aria-modal="true" aria-labelledby="loginTitle" aria-describedby="login_description" tabindex="-1">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Masuk Akun</h2>
            
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="login_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" id="login_email" name="email" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent" placeholder="Masukkan email">
                </div>

                <div>
                    <label for="login_password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" id="login_password" name="password" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent" placeholder="Masukkan password">
                </div>

                <button type="submit" class="w-full py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-all duration-200 hover:shadow-lg active:scale-95 font-semibold">
                    Masuk
                </button>
            </form>

            <div class="mt-4 text-center text-sm text-slate-600">
                Belum punya akun? <button id="toRegister" class="text-slate-900 font-semibold hover:underline transition-colors">Daftar di sini</button>
            </div>

            <button id="closeLoginModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-40 p-4 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl relative">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Daftar Akun</h2>
            
            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="register_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="register_name" name="name" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent" placeholder="Masukkan nama">
                </div>

                <div>
                    <label for="register_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" id="register_email" name="email" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent" placeholder="Masukkan email">
                </div>

                <div>
                    <label for="register_role" class="block text-sm font-medium text-slate-700 mb-1">Pilih Role</label>
                    <select id="register_role" name="role" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent">
                        <option value="">-- Pilih Role --</option>
                        <option value="siswa">Siswa</option>
                        <option value="guru">Guru</option>
                    </select>
                </div>

                <div>
                    <label for="register_password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" id="register_password" name="password" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent" placeholder="Minimal 6 karakter">
                </div>

                <div>
                    <label for="register_password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                    <input type="password" id="register_password_confirmation" name="password_confirmation" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent" placeholder="Ulangi password">
                </div>

                <button type="submit" class="w-full py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-all duration-200 hover:shadow-lg active:scale-95 font-semibold">
                    Daftar
                </button>
            </form>

            <div class="mt-4 text-center text-sm text-slate-600">
                Sudah punya akun? <button id="toLogin" class="text-slate-900 font-semibold hover:underline transition-colors">Masuk di sini</button>
            </div>

            <button id="closeRegisterModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Feature Modal -->
    <div id="featureModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-40 p-4 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl relative">
            <button id="closeFeatureModalIcon" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div id="featureIcon" class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 mx-auto"></div>
            <h2 id="featureTitle" class="text-2xl font-bold text-slate-900 mb-4 text-center"></h2>
            <p id="featureDescription" class="text-slate-600 leading-relaxed mb-6"></p>
            
            @guest
                <button id="featureSignUpBtn" class="w-full py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-all duration-200 hover:shadow-lg active:scale-95 font-semibold mb-3">
                    Daftar untuk Fitur Ini
                </button>
            @endguest
            
            <button id="closeFeatureModal" class="w-full py-2 border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all duration-200 font-semibold">
                Tutup
            </button>
        </div>
    </div>

    <!-- Modal Backdrop -->
    <div id="modalBackdrop" class="hidden fixed inset-0 bg-black/40 z-30 transition-all duration-300"></div>

    <script>
/* Modal + Loader + Navbar scroll + Focus-trap + Esc support */

// elements
const loginModal = document.getElementById('loginModal');
const registerModal = document.getElementById('registerModal');
const featureModal = document.getElementById('featureModal');
const modalBackdrop = document.getElementById('modalBackdrop');
const pageLoader = document.getElementById('pageLoader');
const nav = document.getElementById('mainNav');

const openLoginBtns = document.querySelectorAll('#openLogin');
const openRegisterBtns = document.querySelectorAll('#openRegister');
const closeLoginBtn = document.getElementById('closeLoginModal');
const closeRegisterBtn = document.getElementById('closeRegisterModal');
const closeFeatureBtn = document.getElementById('closeFeatureModal');
const closeFeatureIcon = document.getElementById('closeFeatureModalIcon');
const toLoginBtn = document.getElementById('toLogin');
const toRegisterBtn = document.getElementById('toRegister');
const featureSignUpBtn = document.getElementById('featureSignUpBtn');

let activeModal = null;
let lastFocus = null;

// helpers for animation & aria
function openDialog(modal, dialogContent) {
  if (!modal || !dialogContent) return;
  lastFocus = document.activeElement;
  modal.classList.remove('hidden');
  modalBackdrop.classList.remove('hidden');

  // ensure starting state
  dialogContent.classList.remove('opacity-100','scale-100');
  dialogContent.classList.add('opacity-0','scale-95');

  // small delay to trigger transition
  requestAnimationFrame(() => {
    dialogContent.classList.remove('opacity-0','scale-95');
    dialogContent.classList.add('opacity-100','scale-100');
  });

  activeModal = modal;
  // focus first focusable or close button
  trapFocus(dialogContent);
}

function closeDialog(modal, dialogContent) {
  if (!modal || !dialogContent) return;
  // animate out
  dialogContent.classList.remove('opacity-100','scale-100');
  dialogContent.classList.add('opacity-0','scale-95');
  setTimeout(() => {
    modal.classList.add('hidden');
    if (!loginModal.classList.contains('hidden') || !registerModal.classList.contains('hidden') || !featureModal.classList.contains('hidden')) {
      // some other modal still open
      return;
    }
    modalBackdrop.classList.add('hidden');
  }, 180);
  releaseFocus();
  activeModal = null;
}

// focus trap (simple)
let focusableElementsString = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [tabindex]:not([tabindex="-1"])';
let boundKeyHandler = null;

function trapFocus(container) {
  const focusable = Array.from(container.querySelectorAll(focusableElementsString)).filter(el => el.offsetParent !== null);
  const first = focusable[0] || container;
  const last = focusable[focusable.length - 1] || container;
  first.focus();

  boundKeyHandler = function(e) {
    if (e.key === 'Escape') {
      // close active modal
      if (activeModal === loginModal) closeDialog(loginModal, loginModal.querySelector('[role=\"dialog\"]'));
      if (activeModal === registerModal) closeDialog(registerModal, registerModal.querySelector('[role=\"dialog\"]'));
      if (activeModal === featureModal) closeDialog(featureModal, featureModal.querySelector('[role=\"dialog\"]'));
    }
    if (e.key === 'Tab') {
      if (focusable.length === 0) { e.preventDefault(); return; }
      if (e.shiftKey) { // shift + tab
        if (document.activeElement === first) {
          e.preventDefault();
          last.focus();
        }
      } else {
        if (document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    }
  };
  document.addEventListener('keydown', boundKeyHandler);
}

function releaseFocus() {
  if (boundKeyHandler) {
    document.removeEventListener('keydown', boundKeyHandler);
    boundKeyHandler = null;
  }
  if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
}

// small utility to get dialog content node inside modal
function dialogContent(modal) {
  return modal?.querySelector('[role=\"dialog\"]');
}

// Feature modal content filling
const colorGradients = {
  blue: { from: 'from-blue-100', to: 'to-blue-50', text: 'text-blue-600' },
  green: { from: 'from-green-100', to: 'to-green-50', text: 'text-green-600' },
  purple: { from: 'from-purple-100', to: 'to-purple-50', text: 'text-purple-600' },
  orange: { from: 'from-orange-100', to: 'to-orange-50', text: 'text-orange-600' },
  red: { from: 'from-red-100', to: 'to-red-50', text: 'text-red-600' },
  indigo: { from: 'from-indigo-100', to: 'to-indigo-50', text: 'text-indigo-600' }
};

function openFeatureModal(element, title, description, color) {
  document.getElementById('featureTitle').textContent = title;
  document.getElementById('featureDescription').textContent = description;

  const colorInfo = colorGradients[color] || colorGradients.blue;
  const iconContainer = document.getElementById('featureIcon');
  const svg = element.querySelector('svg')?.cloneNode(true);
  iconContainer.className = `w-14 h-14 bg-gradient-to-br ${colorInfo.from} ${colorInfo.to} rounded-2xl flex items-center justify-center mb-6 mx-auto`;
  iconContainer.innerHTML = '';
  if (svg) {
    svg.setAttribute('class', `w-7 h-7 ${colorInfo.text}`);
    iconContainer.appendChild(svg);
  }
  openDialog(featureModal, dialogContent(featureModal));
}

// attach listeners (handle multiple open buttons)
if (openLoginBtns.length) openLoginBtns.forEach(btn => btn.addEventListener('click', () => openDialog(loginModal, dialogContent(loginModal))));
if (openRegisterBtns.length) openRegisterBtns.forEach(btn => btn.addEventListener('click', () => openDialog(registerModal, dialogContent(registerModal))));

closeLoginBtn?.addEventListener('click', () => closeDialog(loginModal, dialogContent(loginModal)));
closeRegisterBtn?.addEventListener('click', () => closeDialog(registerModal, dialogContent(registerModal)));
closeFeatureBtn?.addEventListener('click', () => closeDialog(featureModal, dialogContent(featureModal)));
closeFeatureIcon?.addEventListener('click', () => closeDialog(featureModal, dialogContent(featureModal)));

toLoginBtn?.addEventListener('click', () => {
  closeDialog(registerModal, dialogContent(registerModal));
  openDialog(loginModal, dialogContent(loginModal));
});
toRegisterBtn?.addEventListener('click', () => {
  closeDialog(loginModal, dialogContent(loginModal));
  openDialog(registerModal, dialogContent(registerModal));
});
featureSignUpBtn?.addEventListener('click', () => {
  closeDialog(featureModal, dialogContent(featureModal));
  openDialog(registerModal, dialogContent(registerModal));
});

modalBackdrop?.addEventListener('click', () => {
  [loginModal, registerModal, featureModal].forEach(m => {
    if (m && !m.classList.contains('hidden')) {
      closeDialog(m, dialogContent(m));
    }
  });
});

// Loader behavior: show only on beforeunload (navigation/refresh)
function hidePageLoader() {
  if (!pageLoader) return;
  pageLoader.classList.add('opacity-0');
  pageLoader.classList.add('pointer-events-none');
  setTimeout(() => pageLoader.classList.add('hidden'), 300);
}
function showPageLoader() {
  if (!pageLoader) return;
  // don't show loader if a modal is active (opening a modal should not trigger)
  if (activeModal) return;
  pageLoader.classList.remove('hidden');
  pageLoader.classList.remove('opacity-0','pointer-events-none');
  pageLoader.classList.add('opacity-100');
}

window.addEventListener('load', () => { setTimeout(hidePageLoader, 120); });
window.addEventListener('beforeunload', () => { showPageLoader(); });

// Navbar scroll effect
window.addEventListener('scroll', () => {
  if (!nav) return;
  if (window.scrollY > 24) {
    nav.classList.add('backdrop-blur-md','bg-white/70','shadow-md');
  } else {
    nav.classList.remove('backdrop-blur-md','bg-white/70','shadow-md');
  }
});
    </script>
</body>
</html>
