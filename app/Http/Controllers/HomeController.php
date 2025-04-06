<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // لا نحتاج إلى middleware auth هنا لأننا نريد عرض الصفحة الرئيسية للزوار غير المسجلين أيضاً
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // إذا كان المستخدم مسجل الدخول، توجيهه للصفحة المناسبة حسب نوعه
        if (auth()->check()) {
            if (auth()->user()->isAgency()) {
                return redirect()->route('agency.dashboard');
            } elseif (auth()->user()->isSubagent()) {
                return redirect()->route('subagent.dashboard');
            } elseif (auth()->user()->isCustomer()) {
                return redirect()->route('customer.dashboard');
            }
        }
        
        // إذا المستخدم غير مسجل دخول أو له نوع غير معروف، عرض الصفحة الرئيسية
        return view('welcome');
    }
}
