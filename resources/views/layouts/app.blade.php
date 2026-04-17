<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Kepegawaian')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
            padding-top: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 1.5rem 1rem;
            color: #fff;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand h4 {
            margin: 0;
            font-weight: 600;
        }
        .sidebar-brand small {
            color: rgba(255,255,255,0.6);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.15);
            border-left: 3px solid #3498db;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .sidebar-heading {
            color: rgba(255,255,255,0.4);
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 1rem 1.5rem 0.5rem;
            letter-spacing: 0.05rem;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .top-navbar {
            background: #fff;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content-wrapper {
            padding: 1.5rem;
        }
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
            border-radius: 8px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        .stat-card {
            border-radius: 10px;
            padding: 1.5rem;
            color: #fff;
        }
        .stat-card.bg-primary { background: linear-gradient(45deg, #3498db, #2980b9); }
        .stat-card.bg-success { background: linear-gradient(45deg, #27ae60, #219a52); }
        .stat-card.bg-warning { background: linear-gradient(45deg, #f39c12, #d68910); }
        .stat-card.bg-info { background: linear-gradient(45deg, #17a2b8, #138496); }
        .stat-card .stat-icon {
            font-size: 3rem;
            opacity: 0.3;
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }
        .table th {
            border-top: none;
            font-weight: 600;
            color: #555;
        }
        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo-tangsel.png') }}" alt="Logo Tangsel" style="width: 50px; height: 50px; margin-bottom: 0.5rem;">
            <h4>SIMPEG DINKES</h4>
            <small>Sistem Kepegawaian</small>
        </div>
        
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            @if(auth()->user()->isAdmin())
            <li class="sidebar-heading">Master Data</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('golongan.*') ? 'active' : '' }}" href="{{ route('golongan.index') }}">
                    <i class="bi bi-layers"></i> Golongan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('jabatan.*') ? 'active' : '' }}" href="{{ route('jabatan.index') }}">
                    <i class="bi bi-briefcase"></i> Jabatan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('unit-kerja.*') ? 'active' : '' }}" href="{{ route('unit-kerja.index') }}">
                    <i class="bi bi-diagram-3"></i> Unit Kerja
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('jenis-cuti.*') ? 'active' : '' }}" href="{{ route('jenis-cuti.index') }}">
                    <i class="bi bi-calendar-check"></i> Jenis Cuti
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('kabupaten-kota.*') ? 'active' : '' }}" href="{{ route('kabupaten-kota.index') }}">
                    <i class="bi bi-geo-alt"></i> Kabupaten/Kota
                </a>
            </li>
            
            <li class="sidebar-heading">Kepegawaian</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}" href="{{ route('pegawai.index') }}">
                    <i class="bi bi-people"></i> Data Pegawai
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('peta-jabatan.*') ? 'active' : '' }}" href="{{ route('peta-jabatan.index') }}">
                    <i class="bi bi-grid-3x3-gap"></i> Peta Jabatan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="bi bi-person-gear"></i> Manajemen User
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('settings.update-schedule.*') ? 'active' : '' }}" href="{{ route('settings.update-schedule.edit') }}">
                    <i class="bi bi-calendar-range"></i> Jadwal Update
                </a>
            </li>
            @elseif(auth()->user()->isSubAdmin())
            <li class="sidebar-heading">Kepegawaian</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}" href="{{ route('pegawai.index') }}">
                    <i class="bi bi-people"></i> Data Pegawai
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('peta-jabatan.*') ? 'active' : '' }}" href="{{ route('peta-jabatan.index') }}">
                    <i class="bi bi-grid-3x3-gap"></i> Peta Jabatan
                </a>
            </li>
            <li class="sidebar-heading">Profil</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profil.*') ? 'active' : '' }}" href="{{ route('profil.index') }}">
                    <i class="bi bi-person-circle"></i> Data Saya
                </a>
            </li>
            @else
            <li class="sidebar-heading">Profil</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profil.*') ? 'active' : '' }}" href="{{ route('profil.index') }}">
                    <i class="bi bi-person-circle"></i> Data Saya
                </a>
            </li>
            @endif
            
            <li class="sidebar-heading">Layanan</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('perjalanan-dinas.*') ? 'active' : '' }}" href="{{ route('perjalanan-dinas.index') }}">
                    <i class="bi bi-airplane"></i> Perjalanan Dinas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cuti.*') ? 'active' : '' }}" href="{{ route('cuti.index') }}">
                    <i class="bi bi-calendar-x"></i> Cuti
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('kgb.*') ? 'active' : '' }}" href="{{ route('kgb.index') }}">
                    <i class="bi bi-cash-stack"></i> KGB
                </a>
            </li>
            
            @if(auth()->user()->isAdmin())
            <li class="sidebar-heading">Pelaporan</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                </a>
            </li>
            @elseif(auth()->user()->isSubAdmin())
            <li class="sidebar-heading">Pelaporan</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                </a>
            </li>
            @endif
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div>
                <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="dropdown">
                <a class="btn btn-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> {{ auth()->user()->username }}
                    <span class="badge bg-{{ auth()->user()->isAdmin() ? 'danger' : 'primary' }} ms-1">
                        {{ auth()->user()->role }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if(auth()->user()->isPegawai())
                    <li><a class="dropdown-item" href="{{ route('profil.index') }}"><i class="bi bi-person"></i> Profil</a></li>
                    @endif
                    <li><a class="dropdown-item" href="{{ route('change-password') }}"><i class="bi bi-key"></i> Ganti Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Content -->
        <div class="content-wrapper">
            @if((auth()->user()->isSubAdmin() || auth()->user()->isPegawai()) && \App\Models\UpdateSchedule::isReadOnlyForUser(auth()->user()))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-lock me-2"></i>Periode update data telah berakhir. Akun Anda saat ini dalam mode <strong>read-only</strong>.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: function() {
                    return $(this).data('placeholder') || '-- Pilih --';
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
