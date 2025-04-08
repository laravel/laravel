<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Agency\DashboardController;
use App\Http\Controllers\Agency\SubagentController;
use App\Http\Controllers\Agency\CustomerController;
use App\Http\Controllers\Agency\ServiceController;
use App\Http\Controllers\Agency\RequestController as AgencyRequestController;
use App\Http\Controllers\Agency\QuoteController as AgencyQuoteController;
use App\Http\Controllers\Agency\TransactionController as AgencyTransactionController;
use App\Http\Controllers\Agency\DocumentController;
use App\Http\Controllers\Agency\SettingsController;
use App\Http\Controllers\Agency\CurrencyController;
use App\Http\Controllers\Agency\ReportController;
use App\Http\Controllers\Subagent\DashboardController as SubagentDashboardController;
use App\Http\Controllers\Subagent\ServiceController as SubagentServiceController;
use App\Http\Controllers\Subagent\RequestController as SubagentRequestController;
use App\Http\Controllers\Subagent\QuoteController as SubagentQuoteController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ServiceController as CustomerServiceController;
use App\Http\Controllers\Customer\RequestController as CustomerRequestController;
use App\Http\Controllers\Customer\QuoteController as CustomerQuoteController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\NotificationController;

// صفحة الترحيب
Route::get('/', function () {
    return view('welcome');
})->name('home');

// تسجيل مسارات المصادقة مرة واحدة فقط
Auth::routes();

// مسارات الملف الشخصي
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// إشعارات المستخدمين (مشتركة لجميع الأنواع)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// مسارات الوكيل الأساسي - usando la clase middleware directamente
Route::prefix('agency')->middleware(['auth', \App\Http\Middleware\AgencyMiddleware::class])->name('agency.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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
    Route::get('/quotes', [AgencyQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quote}', [AgencyQuoteController::class, 'show'])->name('quotes.show');
    Route::post('/quotes/{quote}/approve', [AgencyQuoteController::class, 'approve'])->name('quotes.approve');
    Route::post('/quotes/{quote}/reject', [AgencyQuoteController::class, 'reject'])->name('quotes.reject');
    
    // إدارة المعاملات المالية
    Route::resource('transactions', AgencyTransactionController::class);
    Route::get('/transactions', function () {
        return view('agency.transactions.index');
    })->name('transactions.index');
    
    // إدارة المستندات
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    
    // معلومات الوكالة
    Route::patch('/info', [DashboardController::class, 'updateAgencyInfo'])->name('update-info');
    
    // مسارات التقارير
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/revenue-by-service', [ReportController::class, 'revenueByService'])->name('reports.revenue-by-service');
    Route::get('/reports/revenue-by-subagent', [ReportController::class, 'revenueBySubagent'])->name('reports.revenue-by-subagent');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    
    // إدارة الإعدادات
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // إدارة العملات
    Route::get('/settings/currencies', [CurrencyController::class, 'index'])->name('settings.currencies');
    Route::post('/settings/currencies', [CurrencyController::class, 'store'])->name('settings.currencies.store');
    Route::put('/settings/currencies/{currency}', [CurrencyController::class, 'update'])->name('settings.currencies.update');
    Route::patch('/settings/currencies/{currency}/toggle', [CurrencyController::class, 'toggleStatus'])->name('settings.currencies.toggle-status');
    Route::patch('/settings/currencies/{currency}/default', [CurrencyController::class, 'setAsDefault'])->name('settings.currencies.set-default');
    Route::delete('/settings/currencies/{currency}', [CurrencyController::class, 'destroy'])->name('settings.currencies.destroy');
    
    // الخدمات
    Route::get('/services', [App\Http\Controllers\Agency\ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [App\Http\Controllers\Agency\ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [App\Http\Controllers\Agency\ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}', [App\Http\Controllers\Agency\ServiceController::class, 'show'])->name('services.show');
    Route::get('/services/{service}/edit', [App\Http\Controllers\Agency\ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [App\Http\Controllers\Agency\ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [App\Http\Controllers\Agency\ServiceController::class, 'destroy'])->name('services.destroy');
    
    // الإشعارات
    Route::get('/notifications', [App\Http\Controllers\Agency\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\Agency\NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\Agency\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// مسارات السبوكيل
Route::prefix('subagent')->middleware(['auth', \App\Http\Middleware\SubagentMiddleware::class])->name('subagent.')->group(function () {
    Route::get('/dashboard', [SubagentDashboardController::class, 'index'])->name('dashboard');
    
    // الخدمات المتاحة
    Route::get('/services', [SubagentServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [SubagentServiceController::class, 'show'])->name('services.show');
    
    // طلبات عروض الأسعار
    Route::get('/requests', [SubagentRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{request}', [SubagentRequestController::class, 'show'])->name('requests.show');
    
    // إدارة عروض الأسعار
    Route::get('/quotes', [SubagentQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create/{request}', [SubagentQuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [SubagentQuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}', [SubagentQuoteController::class, 'show'])->name('quotes.show');
    Route::get('/quotes/{quote}/edit', [SubagentQuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('/quotes/{quote}', [SubagentQuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [SubagentQuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::delete('/quotes/{quote}/cancel', [SubagentQuoteController::class, 'destroy'])->name('quotes.cancel');
    
    // الإشعارات
    Route::get('/notifications', [App\Http\Controllers\Subagent\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\Subagent\NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\Subagent\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// مسارات العميل
Route::prefix('customer')->middleware(['auth', \App\Http\Middleware\CustomerMiddleware::class])->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    
    // الخدمات المتاحة
    Route::get('/services', [CustomerServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [CustomerServiceController::class, 'show'])->name('services.show');
    
    // إدارة الطلبات
    Route::resource('requests', CustomerRequestController::class);
    Route::post('/requests/{request}/cancel', [CustomerRequestController::class, 'cancel'])->name('requests.cancel');
    
    // عروض الأسعار
    Route::get('/quotes', [CustomerQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quote}', [CustomerQuoteController::class, 'show'])->name('quotes.show');
    Route::post('/quotes/{quote}/approve', [CustomerQuoteController::class, 'approve'])->name('quotes.approve');
    Route::post('/quotes/{quote}/reject', [CustomerQuoteController::class, 'reject'])->name('quotes.reject');
    
    // الملف الشخصي
    Route::get('/profile', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    
    // الدعم الفني
    Route::get('/support', [App\Http\Controllers\Customer\SupportController::class, 'index'])->name('support');
    Route::post('/support', [App\Http\Controllers\Customer\SupportController::class, 'submit'])->name('support.submit');
    
    // الإشعارات
    Route::get('/notifications', [App\Http\Controllers\Customer\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [App\Http\Controllers\Customer\NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\Customer\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// مسار تحميل المستندات
Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download')->middleware('auth');
