<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Agency\DashboardController as AgencyDashboardController;
use App\Http\Controllers\Agency\SubagentController;
use App\Http\Controllers\Agency\CustomerController;
use App\Http\Controllers\Agency\ServiceController;
use App\Http\Controllers\Agency\RequestController as AgencyRequestController;
use App\Http\Controllers\Agency\QuoteController as AgencyQuoteController;
use App\Http\Controllers\Agency\TransactionController as AgencyTransactionController;
use App\Http\Controllers\Agency\DocumentController;
use App\Http\Controllers\Subagent\DashboardController as SubagentDashboardController;
use App\Http\Controllers\Subagent\ServiceController as SubagentServiceController;
use App\Http\Controllers\Subagent\RequestController as SubagentRequestController;
use App\Http\Controllers\Subagent\QuoteController as SubagentQuoteController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ServiceController as CustomerServiceController;
use App\Http\Controllers\Customer\RequestController as CustomerRequestController;
use App\Http\Controllers\Customer\QuoteController as CustomerQuoteController;

// صفحة الترحيب
Route::get('/', function () {
    return view('welcome');
})->name('home');

// La ruta Auth::routes() ahora está disponible al instalar laravel/ui
Auth::routes();

// مسارات الملف الشخصي
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// مسارات الوكيل الأساسي
Route::prefix('agency')->middleware(['auth', 'agency'])->name('agency.')->group(function () {
    Route::get('/dashboard', [AgencyDashboardController::class, 'index'])->name('dashboard');
    
    // إدارة السبوكلاء
    Route::resource('subagents', SubagentController::class);
    Route::patch('/subagents/{subagent}/toggle-status', [SubagentController::class, 'toggleStatus'])->name('subagents.toggle-status');
    Route::patch('/subagents/{subagent}/update-services', [SubagentController::class, 'updateServices'])->name('subagents.update-services');
    
    // إدارة العملاء
    Route::resource('customers', CustomerController::class);
    Route::patch('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    
    // إدارة الخدمات
    Route::resource('services', ServiceController::class);
    Route::patch('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    
    // إدارة الطلبات
    Route::resource('requests', AgencyRequestController::class);
    Route::patch('/requests/{request}/update-status', [AgencyRequestController::class, 'updateStatus'])->name('requests.update_status');
    Route::post('/requests/{request}/share', [AgencyRequestController::class, 'shareWithSubagents'])->name('requests.share');
    
    // إدارة عروض الأسعار
    Route::resource('quotes', AgencyQuoteController::class);
    Route::post('/quotes/{quote}/approve', [AgencyQuoteController::class, 'approve'])->name('quotes.approve');
    Route::post('/quotes/{quote}/reject', [AgencyQuoteController::class, 'reject'])->name('quotes.reject');
    
    // إدارة المعاملات المالية
    Route::resource('transactions', AgencyTransactionController::class);
    
    // إدارة المستندات
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    
    // معلومات الوكالة
    Route::patch('/info', [AgencyDashboardController::class, 'updateAgencyInfo'])->name('update-info');
});

// مسارات السبوكيل
Route::prefix('subagent')->middleware(['auth', 'subagent'])->name('subagent.')->group(function () {
    Route::get('/dashboard', [SubagentDashboardController::class, 'index'])->name('dashboard');
    
    // الخدمات المتاحة
    Route::get('/services', [SubagentServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [SubagentServiceController::class, 'show'])->name('services.show');
    
    // طلبات عروض الأسعار
    Route::get('/requests', [SubagentRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{request}', [SubagentRequestController::class, 'show'])->name('requests.show');
    
    // إدارة عروض الأسعار
    Route::post('/quotes', [SubagentQuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes', [SubagentQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quote}', [SubagentQuoteController::class, 'show'])->name('quotes.show');
    Route::put('/quotes/{quote}', [SubagentQuoteController::class, 'update'])->name('quotes.update');
});

// مسارات العميل
Route::prefix('customer')->middleware(['auth', 'customer'])->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    
    // الخدمات المتاحة
    Route::get('/services', [CustomerServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [CustomerServiceController::class, 'show'])->name('services.show');
    
    // إدارة الطلبات
    Route::resource('requests', CustomerRequestController::class);
    
    // عروض الأسعار
    Route::get('/quotes', [CustomerQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quote}', [CustomerQuoteController::class, 'show'])->name('quotes.show');
    Route::post('/quotes/{quote}/approve', [CustomerQuoteController::class, 'approve'])->name('quotes.approve');
    Route::post('/quotes/{quote}/reject', [CustomerQuoteController::class, 'reject'])->name('quotes.reject');
});

// مسار تحميل المستندات
Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download')->middleware('auth');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
