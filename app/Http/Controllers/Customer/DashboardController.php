<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * عرض لوحة تحكم العميل.
     */
    public function index()
    {
        return view('customer.dashboard');
    }
}
