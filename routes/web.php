<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\CMSController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\UniversityController as PublicUniversityController;
use App\Http\Controllers\Public\CollegeController as PublicCollegeController;
use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\Public\EnquiryController as PublicEnquiryController;
use App\Http\Controllers\Public\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Public Website Routes
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/universities', [PublicUniversityController::class, 'index'])->name('universities');
Route::get('/universities/{slug}', [PublicUniversityController::class, 'show'])->name('university.detail');
Route::get('/colleges', [PublicCollegeController::class, 'index'])->name('colleges');
Route::get('/colleges/{slug}', [PublicCollegeController::class, 'show'])->name('college.detail');
Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses');
Route::get('/courses/{slug}', [PublicCourseController::class, 'show'])->name('course.detail');
Route::get('/enquiry', [PublicEnquiryController::class, 'create'])->name('enquiry');
Route::post('/enquiry', [PublicEnquiryController::class, 'store'])->name('enquiry.store');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Admin Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/admin/login', [LoginController::class, 'login']);
    Route::get('/admin/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/admin/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/admin/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/admin/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Admin Panel Routes (Authenticated)
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('admin.profile.change-password');
    Route::put('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('admin.profile.update-password');

    // University Routes
    Route::resource('universities', UniversityController::class)->names([
        'index' => 'admin.universities.index',
        'create' => 'admin.universities.create',
        'store' => 'admin.universities.store',
        'show' => 'admin.universities.show',
        'edit' => 'admin.universities.edit',
        'update' => 'admin.universities.update',
        'destroy' => 'admin.universities.destroy',
    ]);

    // College Routes
    Route::resource('colleges', CollegeController::class)->names([
        'index' => 'admin.colleges.index',
        'create' => 'admin.colleges.create',
        'store' => 'admin.colleges.store',
        'show' => 'admin.colleges.show',
        'edit' => 'admin.colleges.edit',
        'update' => 'admin.colleges.update',
        'destroy' => 'admin.colleges.destroy',
    ]);

    // Course Routes
    Route::resource('courses', CourseController::class)->names([
        'index' => 'admin.courses.index',
        'create' => 'admin.courses.create',
        'store' => 'admin.courses.store',
        'show' => 'admin.courses.show',
        'edit' => 'admin.courses.edit',
        'update' => 'admin.courses.update',
        'destroy' => 'admin.courses.destroy',
    ]);

    // Student Routes
    Route::resource('students', StudentController::class)->names([
        'index' => 'admin.students.index',
        'create' => 'admin.students.create',
        'store' => 'admin.students.store',
        'show' => 'admin.students.show',
        'edit' => 'admin.students.edit',
        'update' => 'admin.students.update',
        'destroy' => 'admin.students.destroy',
    ]);

    // CMS Routes
    Route::resource('cms', CMSController::class)->names([
        'index' => 'admin.cms.index',
        'create' => 'admin.cms.create',
        'store' => 'admin.cms.store',
        'show' => 'admin.cms.show',
        'edit' => 'admin.cms.edit',
        'update' => 'admin.cms.update',
        'destroy' => 'admin.cms.destroy',
    ]);

    // Enquiry Routes
    Route::resource('enquiries', EnquiryController::class)->only(['index', 'show', 'edit', 'update', 'destroy'])->names([
        'index' => 'admin.enquiries.index',
        'show' => 'admin.enquiries.show',
        'edit' => 'admin.enquiries.edit',
        'update' => 'admin.enquiries.update',
        'destroy' => 'admin.enquiries.destroy',
    ]);

    // Settings Routes
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::put('/notifications/{notification}/mark-read', [NotificationController::class, 'markRead'])->name('admin.notifications.mark-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');

    // Support Routes
    Route::get('/support', [SupportController::class, 'index'])->name('admin.support.index');
    Route::post('/support', [SupportController::class, 'store'])->name('admin.support.store');
});