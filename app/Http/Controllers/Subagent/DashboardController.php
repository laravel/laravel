<?php

namespace App\Http\Controllers\Subagent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Quote;
use App\Models\Request as ServiceRequest;

class DashboardController extends Controller
{
    /**
     * عرض لوحة تحكم السبوكيل.
     */
    public function index()
    {
        // الخدمات المتاحة للسبوكيل
        $services = Service::join('service_subagent', 'services.id', '=', 'service_subagent.service_id')
                         ->where('service_subagent.user_id', auth()->id())
                         ->where('service_subagent.is_active', true)
                         ->count();
        
        // إحصائيات عروض الأسعار
        $pendingQuotes = Quote::where('subagent_id', auth()->id())
                            ->where('status', 'pending')
                            ->count();
        
        $approvedQuotes = Quote::where('subagent_id', auth()->id())
                             ->whereIn('status', ['agency_approved', 'customer_approved'])
                             ->count();
        
        $rejectedQuotes = Quote::where('subagent_id', auth()->id())
                             ->whereIn('status', ['agency_rejected', 'customer_rejected'])
                             ->count();
        
        // آخر عروض الأسعار
        $recentQuotes = Quote::where('subagent_id', auth()->id())
                           ->with(['request', 'request.service'])
                           ->latest()
                           ->take(5)
                           ->get();
        
        // الطلبات المتاحة لعروض الأسعار
        $availableRequests = ServiceRequest::whereHas('service', function($query) {
                                  $query->whereHas('subagents', function($q) {
                                      $q->where('users.id', auth()->id())
                                        ->where('service_subagent.is_active', true);
                                  });
                              })
                              ->where('status', 'pending')
                              ->count();
        
        return view('subagent.dashboard', compact(
            'services',
            'pendingQuotes',
            'approvedQuotes',
            'rejectedQuotes',
            'recentQuotes',
            'availableRequests'
        ));
    }
}
