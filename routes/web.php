<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GolonganController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\JenisCutiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\KabupatenKotaController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PerjalananDinasController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\KgbController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PetaJabatanController;
use App\Http\Controllers\UpdateScheduleController;

// Auth Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit')->middleware('throttle:login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth', 'force_password_change', 'enforce_update_schedule'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profil (untuk pegawai update data sendiri)
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    
    // Perjalanan Dinas
    Route::resource('perjalanan-dinas', PerjalananDinasController::class);
    Route::post('/perjalanan-dinas/{perjalananDina}/approve', [PerjalananDinasController::class, 'approve'])
        ->name('perjalanan-dinas.approve')
        ->middleware('admin');
    Route::post('/perjalanan-dinas/{perjalananDina}/selesai', [PerjalananDinasController::class, 'selesai'])
        ->name('perjalanan-dinas.selesai')
        ->middleware('admin');
    
    // Cuti
    Route::get('/cuti/saldo', [CutiController::class, 'saldo'])->name('cuti.saldo');
    Route::resource('cuti', CutiController::class);
    Route::post('/cuti/{cuti}/approve', [CutiController::class, 'approve'])
        ->name('cuti.approve')
        ->middleware('admin');
    Route::post('/cuti/{cuti}/selesai', [CutiController::class, 'selesai'])
        ->name('cuti.selesai')
        ->middleware('admin');
    
    // KGB
    Route::resource('kgb', KgbController::class);
    Route::get('/kgb/pegawai/{pegawai}', [KgbController::class, 'getPegawaiInfo'])->name('kgb.pegawai-info');
    Route::post('/kgb/{kgb}/approve', [KgbController::class, 'approve'])
        ->name('kgb.approve')
        ->middleware('admin');
    
    // Change Password
    Route::get('/change-password', [UserController::class, 'changePassword'])->name('change-password');
    Route::put('/change-password', [UserController::class, 'updatePassword'])->name('change-password.update');
    
    // Admin or Sub Admin Routes (Pegawai Management)
    Route::middleware('admin_or_subadmin')->group(function () {
        // Pegawai Management - Sub Admin can access pegawai from their unit kerja only
        Route::resource('pegawai', PegawaiController::class);
        Route::patch('/pegawai/{pegawai}/toggle-status', [PegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');
        Route::get('/api/jabatan-by-eselon', [PegawaiController::class, 'getJabatanByEselon'])->name('api.jabatan-by-eselon');
        
        // Peta Jabatan per Unit Kerja
        Route::prefix('peta-jabatan')->name('peta-jabatan.')->group(function () {
            Route::get('/', [PetaJabatanController::class, 'index'])->name('index');
            Route::get('/set-kebutuhan', [PetaJabatanController::class, 'setKebutuhan'])->name('set-kebutuhan');
            Route::post('/store-kebutuhan', [PetaJabatanController::class, 'storeKebutuhan'])->name('store-kebutuhan');
            Route::get('/detail/{unit_kerja_id}/{jabatan_id}', [PetaJabatanController::class, 'detailPegawai'])->name('detail-pegawai');
            Route::get('/export', [PetaJabatanController::class, 'export'])->name('export');
            Route::get('/print', [PetaJabatanController::class, 'print'])->name('print');
            Route::get('/rekap', [PetaJabatanController::class, 'rekap'])->name('rekap');
            Route::get('/rekap-uptd-puskesmas', [PetaJabatanController::class, 'rekapUptdPuskesmas'])->name('rekap-uptd-puskesmas');
            Route::get('/rekap/print', [PetaJabatanController::class, 'printRekap'])->name('rekap-print');
        });
        
        // Laporan - Sub Admin can see reports for their unit kerja only
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/pegawai', [LaporanController::class, 'pegawai'])->name('pegawai');
            Route::get('/golongan', [LaporanController::class, 'golongan'])->name('golongan');
            Route::get('/jabatan', [LaporanController::class, 'jabatan'])->name('jabatan');
            Route::get('/eselon', [LaporanController::class, 'eselon'])->name('eselon');
            Route::get('/unit-kerja', [LaporanController::class, 'unitKerja'])->name('unit-kerja');
            Route::get('/pensiun', [LaporanController::class, 'pensiun'])->name('pensiun');
            Route::get('/usia', [LaporanController::class, 'usia'])->name('usia');
            Route::get('/pendidikan', [LaporanController::class, 'pendidikan'])->name('pendidikan');
            Route::get('/agama', [LaporanController::class, 'agama'])->name('agama');
            Route::get('/statistik', [LaporanController::class, 'statistik'])->name('statistik');
            Route::get('/export/{type}', [LaporanController::class, 'exportExcel'])->name('export');
            Route::get('/print/{type}', [LaporanController::class, 'print'])->name('print');
        });
    });
    
    // Admin Only Routes
    Route::middleware('admin')->group(function () {
        // Settings - Update Schedule
        Route::get('/settings/update-schedule', [UpdateScheduleController::class, 'edit'])->name('settings.update-schedule.edit');
        Route::put('/settings/update-schedule', [UpdateScheduleController::class, 'update'])->name('settings.update-schedule.update');

        // Master Data - Golongan
        Route::resource('golongan', GolonganController::class)->except(['show']);
        
        // Master Data - Jabatan
        Route::resource('jabatan', JabatanController::class)->except(['show']);
        
        // Master Data - Unit Kerja
        Route::resource('unit-kerja', UnitKerjaController::class)->except(['show']);
        
        // Master Data - Jenis Cuti
        Route::resource('jenis-cuti', JenisCutiController::class)->except(['show']);
        
        // Master Data - Kabupaten/Kota
        Route::resource('kabupaten-kota', KabupatenKotaController::class)->except(['show']);
        
        // User Management
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });
});
