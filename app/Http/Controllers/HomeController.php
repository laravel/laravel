<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * إنشاء نموذج تحكم جديد.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * عرض لوحة التحكم الرئيسية.
     */
    public function index()
    {
        // توجيه المستخدم حسب نوعه
        if (auth()->check()) {
            if (auth()->user()->isAgency()) {
                return redirect()->route('agency.dashboard');
            } elseif (auth()->user()->isSubagent()) {
                return redirect()->route('subagent.dashboard');
            } elseif (auth()->user()->isCustomer()) {
                return redirect()->route('customer.dashboard');
            }
        }
        
        return view('welcome');
    }
}
