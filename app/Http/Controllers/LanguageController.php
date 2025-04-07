<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * تغيير لغة التطبيق
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeLanguage(Request $request)
    {
        $request->validate([
            'locale' => 'required|string|in:' . implode(',', config('app.available_locales', ['ar'])),
        ]);

        $locale = $request->locale;
        
        // حفظ اللغة المحددة في الجلسة
        Session::put('locale', $locale);
        
        // تغيير لغة التطبيق الحالية
        App::setLocale($locale);
        
        // ضبط اتجاه الصفحة بناءً على اللغة
        $direction = in_array($locale, ['ar']) ? 'rtl' : 'ltr';
        Session::put('direction', $direction);
        
        return redirect()->back()->with('success', __('messages.language_changed'));
    }
}
