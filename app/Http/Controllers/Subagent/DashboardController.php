<?php

namespace App\Http\Controllers\Subagent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\Request as ServiceRequest;

class DashboardController extends Controller
{
    /**
     * عرض لوحة تحكم السبوكيل
     */
    public function index()
    {
        $subagentId = auth()->id();
        
        // الخدمات المتاحة
        $servicesCount = auth()->user()->services->count();
        
        // طلبات عروض الأسعار
        $requestsCount = ServiceRequest::whereHas('service', function($query) use ($subagentId) {
            $query->whereHas('subagents', function($q) use ($subagentId) {
                $q->where('users.id', $subagentId);
            });
        })->count();
        
        // العروض المقبولة
        $approvedQuotesCount = Quote::where('subagent_id', $subagentId)
            ->whereIn('status', ['agency_approved', 'customer_approved'])
            ->count();
        
        // آخر الطلبات المتاحة
        $latestRequests = ServiceRequest::whereHas('service', function($query) use ($subagentId) {
            $query->whereHas('subagents', function($q) use ($subagentId) {
                $q->where('users.id', $subagentId);
            });
        })
        ->latest()
        ->take(5)
        ->get();
        
        // بيانات الرسم البياني
        $pendingQuotesCount = Quote::where('subagent_id', $subagentId)
            ->where('status', 'pending')
            ->count();
            
        $agencyApprovedQuotesCount = Quote::where('subagent_id', $subagentId)
            ->where('status', 'agency_approved')
            ->count();
            
        $customerApprovedQuotesCount = Quote::where('subagent_id', $subagentId)
            ->where('status', 'customer_approved')
            ->count();
            
        $rejectedQuotesCount = Quote::where('subagent_id', $subagentId)
            ->whereIn('status', ['agency_rejected', 'customer_rejected'])
            ->count();
        
        // المستحقات المالية
        $completedCommissions = Transaction::where('user_id', $subagentId)
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->sum('amount');
            
        $pendingCommissions = Transaction::where('user_id', $subagentId)
            ->where('type', 'commission')
            ->where('status', 'pending')
            ->sum('amount');
        
        return view('subagent.dashboard', compact(
            'servicesCount',
            'requestsCount',
            'approvedQuotesCount',
            'latestRequests',
            'pendingQuotesCount',
            'agencyApprovedQuotesCount',
            'customerApprovedQuotesCount',
            'rejectedQuotesCount',
            'completedCommissions',
            'pendingCommissions'
        ));
    }
}
