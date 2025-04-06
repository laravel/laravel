<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\Request as ServiceRequest;

class QuoteController extends Controller
{
    /**
     * عرض قائمة عروض الأسعار المقدمة للعميل.
     */
    public function index(Request $request)
    {
        $query = Quote::whereHas('request', function($q) {
            $q->where('customer_id', auth()->id());
        });

        // تطبيق عوامل التصفية
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('request_id') && !empty($request->request_id)) {
            $query->where('request_id', $request->request_id);
        }

        // ترتيب وتصنيف النتائج
        $quotes = $query->latest()->paginate(10);
        
        // الحصول على الطلبات للتصفية
        $requests = ServiceRequest::where('customer_id', auth()->id())->get();
                         
        return view('customer.quotes.index', compact('quotes', 'requests'));
    }

    /**
     * عرض تفاصيل عرض سعر معين.
     */
    public function show(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للعميل
        if ($quote->request->customer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        return view('customer.quotes.show', compact('quote'));
    }

    /**
     * قبول عرض سعر.
     */
    public function approve(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للعميل
        if ($quote->request->customer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        // التحقق من أن العرض معتمد من الوكالة
        if ($quote->status !== 'agency_approved') {
            return redirect()->back()->with('error', 'لا يمكن قبول عرض لم يتم اعتماده من الوكالة.');
        }
        
        // تغيير حالة العرض إلى مقبول من العميل
        $quote->update([
            'status' => 'customer_approved'
        ]);
        
        // تغيير حالة الطلب إلى قيد التنفيذ
        $quote->request->update([
            'status' => 'in_progress'
        ]);
        
        // رفض باقي العروض المقدمة لنفس الطلب
        Quote::where('request_id', $quote->request_id)
            ->where('id', '!=', $quote->id)
            ->where('status', 'agency_approved')
            ->update([
                'status' => 'customer_rejected'
            ]);
        
        return redirect()->back()->with('success', 'تم قبول العرض بنجاح. سيتم العمل على طلبك قريباً.');
    }

    /**
     * رفض عرض سعر.
     */
    public function reject(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للعميل
        if ($quote->request->customer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        // التحقق من أن العرض معتمد من الوكالة
        if ($quote->status !== 'agency_approved') {
            return redirect()->back()->with('error', 'لا يمكن رفض عرض لم يتم اعتماده من الوكالة.');
        }
        
        // تغيير حالة العرض إلى مرفوض من العميل
        $quote->update([
            'status' => 'customer_rejected'
        ]);
        
        return redirect()->back()->with('success', 'تم رفض العرض بنجاح.');
    }
}
