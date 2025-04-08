<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات المتاحة للعميل.
     */
    public function index()
    {
        $agency_id = auth()->user()->agency_id;
        
        // الحصول على الخدمات النشطة للوكالة
        $services = Service::where('agency_id', $agency_id)
                         ->where('status', 'active')
                         ->orderBy('type')
                         ->get()
                         ->groupBy('type');
        
        return view('customer.services.index', compact('services'));
    }

    /**
     * عرض تفاصيل خدمة معينة.
     */
    public function show(Service $service)
    {
        // التحقق من أن الخدمة تنتمي لنفس وكالة العميل وأنها نشطة
        if ($service->agency_id !== auth()->user()->agency_id || $service->status !== 'active') {
            abort(404, 'الخدمة غير متوفرة');
        }
        
        return view('customer.services.show', compact('service'));
    }
}
