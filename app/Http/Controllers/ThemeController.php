<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ThemeController extends Controller
{
    /**
     * تبديل وضع السمة (فاتح/داكن)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:light,dark,auto',
        ]);

        $theme = $request->theme;
        
        // إذا كان المستخدم مسجل دخول، احفظ التفضيل في قاعدة البيانات
        if (Auth::check()) {
            $user = Auth::user();
            $user->theme_preference = $theme;
            $user->save();
        }
        
        // احفظ التفضيل في كوكي لمدة سنة
        Cookie::queue('theme_preference', $theme, 60 * 24 * 365);
        
        return response()->json([
            'success' => true,
            'theme' => $theme,
            'message' => __('messages.theme_updated')
        ]);
    }
}
