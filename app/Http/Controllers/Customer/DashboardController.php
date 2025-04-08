<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as ServiceRequest;
use App\Models\Quote;
use App\Models\Service;

class DashboardController extends Controller
{
    /**
     * عرض صفحة لوحة التحكم
     */
    public function index()
    {
        $customerId = auth()->id();
        $agencyId = auth()->user()->agency_id;

        // إحصائيات الطلبات
        $counts = [
            'requests' => ServiceRequest::where('customer_id', $customerId)->count(),
            'quotes' => Quote::whereHas('request', function($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })->count(),
            'completed' => ServiceRequest::where('customer_id', $customerId)->where('status', 'completed')->count(),
            'in_progress' => ServiceRequest::where('customer_id', $customerId)->where('status', 'in_progress')->count(),
        ];

        // الطلبات الأخيرة
        $recentRequests = ServiceRequest::with('service')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->each(function($request) {
                $request->status_badge = $this->getStatusBadge($request->status);
                $request->status_text = $this->getStatusText($request->status);
            });

        // عروض الأسعار الأخيرة
        $recentQuotes = Quote::with(['request.service'])
            ->whereHas('request', function($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->each(function($quote) {
                $quote->status_badge = $this->getQuoteStatusBadge($quote->status);
                $quote->status_text = $this->getQuoteStatusText($quote->status);
            });

        // خدمات مقترحة
        $suggestedServices = Service::where('agency_id', $agencyId)
            ->where('status', 'active')
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('customer.dashboard', compact('counts', 'recentRequests', 'recentQuotes', 'suggestedServices'));
    }

    /**
     * الحصول على لون خلفية حالة الطلب
     */
    private function getStatusBadge($status)
    {
        switch ($status) {
            case 'pending':
                return 'warning';
            case 'in_progress':
                return 'info';
            case 'completed':
                return 'success';
            case 'cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * الحصول على نص حالة الطلب
     */
    private function getStatusText($status)
    {
        switch ($status) {
            case 'pending':
                return 'قيد الانتظار';
            case 'in_progress':
                return 'قيد التنفيذ';
            case 'completed':
                return 'مكتمل';
            case 'cancelled':
                return 'ملغي';
            default:
                return $status;
        }
    }

    /**
     * الحصول على لون خلفية حالة عرض السعر
     */
    private function getQuoteStatusBadge($status)
    {
        switch ($status) {
            case 'pending':
                return 'warning';
            case 'agency_approved':
                return 'info';
            case 'customer_approved':
                return 'success';
            case 'rejected':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * الحصول على نص حالة عرض السعر
     */
    private function getQuoteStatusText($status)
    {
        switch ($status) {
            case 'pending':
                return 'قيد المراجعة';
            case 'agency_approved':
                return 'معتمد من الوكالة';
            case 'customer_approved':
                return 'تم القبول';
            case 'rejected':
                return 'مرفوض';
            default:
                return $status;
        }
    }
}
