<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\Request as ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\QuoteStatusChanged;

class QuoteController extends Controller
{
    /**
     * عرض قائمة عروض الأسعار للعميل
     */
    public function index(Request $request)
    {
        $query = Quote::whereHas('request', function($q) {
            $q->where('customer_id', Auth::id());
        });

        // تطبيق عوامل التصفية
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('service_id') && !empty($request->service_id)) {
            $query->whereHas('request', function($q) use ($request) {
                $q->where('service_id', $request->service_id);
            });
        }

        // ترتيب وتصنيف النتائج
        $quotes = $query->with(['request.service', 'subagent'])
                       ->latest()
                       ->paginate(10);

        // الحصول على الخدمات للتصفية
        $services = \App\Models\Service::whereHas('requests', function($q) {
            $q->where('customer_id', Auth::id());
        })->get();

        return view('customer.quotes.index', compact('quotes', 'services'));
    }

    /**
     * عرض تفاصيل عرض سعر معين
     */
    public function show(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للعميل
        if ($quote->request->customer_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }

        // تحميل العلاقات
        $quote->load(['request.service', 'subagent', 'attachments']);

        return view('customer.quotes.show', compact('quote'));
    }

    /**
     * قبول عرض سعر
     */
    public function approve(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للعميل
        if ($quote->request->customer_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }

        // التحقق من أن حالة عرض السعر تسمح بالقبول
        if ($quote->status !== 'agency_approved') {
            return redirect()->route('customer.quotes.show', $quote)
                           ->with('error', 'لا يمكن قبول هذا العرض في حالته الحالية');
        }

        // تحديث حالة عرض السعر
        $quote->update([
            'status' => 'customer_approved',
        ]);

        // تحديث حالة الطلب إلى قيد التنفيذ
        $quote->request->update([
            'status' => 'in_progress',
        ]);

        // إرسال إشعارات
        try {
            $quote->subagent->notify(new QuoteStatusChanged($quote));
            // إشعار للوكالة
            $agency = \App\Models\User::where('agency_id', $quote->request->agency_id)
                               ->where('user_type', 'agency')
                               ->first();
            if ($agency) {
                $agency->notify(new QuoteStatusChanged($quote));
            }
        } catch (\Exception $e) {
            \Log::error('Error sending notification: ' . $e->getMessage());
        }

        return redirect()->route('customer.quotes.show', $quote)
                        ->with('success', 'تم قبول عرض السعر بنجاح وسيبدأ العمل على طلبك قريباً');
    }

    /**
     * رفض عرض سعر
     */
    public function reject(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للعميل
        if ($quote->request->customer_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }

        // التحقق من أن حالة عرض السعر تسمح بالرفض
        if ($quote->status !== 'agency_approved') {
            return redirect()->route('customer.quotes.show', $quote)
                           ->with('error', 'لا يمكن رفض هذا العرض في حالته الحالية');
        }

        // تحديث حالة عرض السعر
        $quote->update([
            'status' => 'customer_rejected',
        ]);

        // إرسال إشعارات
        try {
            $quote->subagent->notify(new QuoteStatusChanged($quote));
            // إشعار للوكالة
            $agency = \App\Models\User::where('agency_id', $quote->request->agency_id)
                               ->where('user_type', 'agency')
                               ->first();
            if ($agency) {
                $agency->notify(new QuoteStatusChanged($quote));
            }
        } catch (\Exception $e) {
            \Log::error('Error sending notification: ' . $e->getMessage());
        }

        return redirect()->route('customer.quotes.show', $quote)
                        ->with('success', 'تم رفض عرض السعر بنجاح');
    }
}
