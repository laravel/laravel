<?php

namespace App\Http\Controllers\Subagent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\Request as ServiceRequest;

class QuoteController extends Controller
{
    /**
     * عرض قائمة عروض الأسعار المقدمة من السبوكيل.
     */
    public function index(Request $request)
    {
        $query = Quote::where('subagent_id', auth()->id());

        // تطبيق عوامل التصفية
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'approved') {
                $query->whereIn('status', ['agency_approved', 'customer_approved']);
            } elseif ($request->status === 'rejected') {
                $query->whereIn('status', ['agency_rejected', 'customer_rejected']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('request_id') && !empty($request->request_id)) {
            $query->where('request_id', $request->request_id);
        }

        if ($request->has('service_id') && !empty($request->service_id)) {
            $query->whereHas('request', function($q) use ($request) {
                $q->where('service_id', $request->service_id);
            });
        }

        // ترتيب وتصنيف النتائج
        $quotes = $query->with(['request', 'request.service', 'request.customer'])
                       ->latest()
                       ->paginate(10);
        
        // الحصول على الطلبات للتصفية
        $requests = ServiceRequest::whereHas('quotes', function($q) {
                                    $q->where('subagent_id', auth()->id());
                                })
                                ->get();
        
        // الحصول على الخدمات للتصفية
        $services = auth()->user()->services()
                          ->where('status', 'active')
                          ->where('service_subagent.is_active', true)
                          ->get();
                         
        return view('subagent.quotes.index', compact('quotes', 'requests', 'services'));
    }

    /**
     * عرض تفاصيل عرض سعر معين.
     */
    public function show(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للسبوكيل
        if ($quote->subagent_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        return view('subagent.quotes.show', compact('quote'));
    }

    /**
     * عرض نموذج تعديل عرض سعر.
     */
    public function edit(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للسبوكيل
        if ($quote->subagent_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        // التحقق من أن العرض في حالة تسمح بالتعديل
        if ($quote->status !== 'pending' && $quote->status !== 'agency_rejected') {
            return redirect()->route('subagent.quotes.show', $quote)
                            ->with('error', 'لا يمكن تعديل هذا العرض في وضعه الحالي.');
        }
        
        return view('subagent.quotes.edit', compact('quote'));
    }

    /**
     * تحديث عرض سعر معين.
     */
    public function update(Request $request, Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للسبوكيل
        if ($quote->subagent_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        // التحقق من أن العرض في حالة تسمح بالتعديل
        if ($quote->status !== 'pending' && $quote->status !== 'agency_rejected') {
            return redirect()->route('subagent.quotes.show', $quote)
                            ->with('error', 'لا يمكن تعديل هذا العرض في وضعه الحالي.');
        }
        
        $request->validate([
            'price' => 'required|numeric|min:0',
            'details' => 'required|string',
        ]);
        
        // الحصول على معدل العمولة المخصص للسبوكيل على هذه الخدمة
        $serviceSubagent = $quote->request->service->subagents()
                                ->where('users.id', auth()->id())
                                ->first();
        
        $commissionRate = $serviceSubagent ? $serviceSubagent->pivot->custom_commission_rate : $quote->request->service->commission_rate;
        
        // حساب مبلغ العمولة
        $commissionAmount = $request->price * ($commissionRate / 100);
        
        // تحديث عرض السعر
        $quote->update([
            'price' => $request->price,
            'details' => $request->details,
            'status' => 'pending', // إعادة تعيين الحالة إلى "قيد الانتظار"
            'commission_amount' => $commissionAmount,
            'commission_rate' => $commissionRate,
        ]);
        
        return redirect()->route('subagent.quotes.index')
                        ->with('success', 'تم تحديث عرض السعر بنجاح. بانتظار مراجعته من قبل الوكالة.');
    }

    /**
     * إلغاء عرض سعر معين.
     */
    public function cancel(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للسبوكيل
        if ($quote->subagent_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        // التحقق من أن العرض في حالة تسمح بالإلغاء
        if ($quote->status !== 'pending') {
            return redirect()->route('subagent.quotes.show', $quote)
                            ->with('error', 'لا يمكن إلغاء هذا العرض في وضعه الحالي.');
        }
        
        // حذف عرض السعر
        $quote->delete();
        
        return redirect()->route('subagent.quotes.index')
                        ->with('success', 'تم إلغاء عرض السعر بنجاح.');
    }
}
