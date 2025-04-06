<?php

namespace App\Http\Controllers\Subagent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات المتاحة للسبوكيل.
     */
    public function index()
    {
        // الحصول على الخدمات المتاحة للسبوكيل المسجل الدخول
        $services = Service::join('service_subagent', 'services.id', '=', 'service_subagent.service_id')
                        ->where('service_subagent.user_id', auth()->id())
                        ->where('service_subagent.is_active', true)
                        ->where('services.status', 'active')
                        ->select('services.*', 'service_subagent.custom_commission_rate')
                        ->get()
                        ->groupBy('type');
        
        return view('subagent.services.index', compact('services'));
    }

    /**
     * عرض تفاصيل خدمة معينة.
     */
    public function show(Service $service)
    {
        // التحقق من أن الخدمة متاحة للسبوكيل
        $serviceSubagent = $service->subagents()
                                ->where('users.id', auth()->id())
                                ->where('service_subagent.is_active', true)
                                ->first();
        
        if (!$serviceSubagent || $service->status !== 'active') {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخدمة');
        }
        
        // الحصول على تاريخ الطلبات المتعلقة بهذه الخدمة
        $requestsHistory = $service->requests()
                                ->whereHas('quotes', function($query) {
                                    $query->where('subagent_id', auth()->id());
                                })
                                ->with('quotes')
                                ->latest()
                                ->take(10)
                                ->get();
        
        return view('subagent.services.show', compact('service', 'serviceSubagent', 'requestsHistory'));
    }
}
