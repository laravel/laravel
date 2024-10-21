<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SecurityScanner;
use Illuminate\Support\Facades\Log;

// في حالة اكتشاف ثغرة:SQL Injection
Log::warning('Potential SQL Injection detected in field: ');
// في حالة اكتشاف ثغرة:XSS
Log::warning('Potential Cross-Site Scripting (XSS) detected in field: ');
// في حالة اكتشاف ثغرة:CSRF
Log::warning('Potential Cross-Site Request Forgery (CSRF) detected in field: ');
// في حالة اكتشاف ثغرة:



Route::middleware([SecurityScanner::class])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    // إضافة المزيد من التوجيهات هنا
});
