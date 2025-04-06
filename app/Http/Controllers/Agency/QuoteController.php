<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\Service;
use App\Models\User;
use App\Models\Request as ServiceRequest;

class QuoteController extends Controller
{
    /**
     * عرض قائمة عروض الأسعار
     */
    public function index(Request $request)
    {
        $query = Quote::whereHas('request', function($q) {
            $q->where('agency_id', auth()->user()->agency_id);
        });

        // تطبيق فلاتر البحث
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('subagent_id') && !empty($request->subagent_id)) {
            $query->where('subagent_id', $request->subagent_id);
        }

        if ($request->has('service_id') && !empty($request->service_id)) {
            $query->whereHas('request', function($q) use ($request) {
                $q->where('service_id', $request->service_id);
            });
        }

        // ترتيب النتائج
        $quotes = $query->latest()->paginate(10);

        // استدعاء قوائم السبوكلاء والخدمات للفلترة
        $subagents = User::where('agency_id', auth()->user()->agency_id)
                         ->where('user_type', 'subagent')
                         ->get();
                         
        $services = Service::where('agency_id', auth()->user()->agency_id)->get();

        return view('agency.quotes.index', compact('quotes', 'subagents', 'services'));
    }

    /**
     * عرض نموذج إنشاء عرض سعر
     */
    public function create(Request $request)
    {
        $request_id = $request->query('request_id');
        $serviceRequest = null;
        
        if ($request_id) {
            $serviceRequest = ServiceRequest::where('agency_id', auth()->user()->agency_id)
                                      ->findOrFail($request_id);
        }
        
        $requests = ServiceRequest::where('agency_id', auth()->user()->agency_id)
                                 ->orderBy('created_at', 'desc')
                                 ->get();
                                 
        $subagents = User::where('agency_id', auth()->user()->agency_id)
                         ->where('user_type', 'subagent')
                         ->where('is_active', true)
                         ->get();
                         
        return view('agency.quotes.create', compact('requests', 'subagents', 'serviceRequest'));
    }

    /**
     * تخزين عرض سعر جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:requests,id',
            'subagent_id' => 'required|exists:users,id',
            'price' => 'required|numeric|min:0',
            'commission_amount' => 'required|numeric|min:0',
            'details' => 'required|string',
        ]);
        
        // التحقق من أن الطلب ينتمي للوكالة
        $serviceRequest = ServiceRequest::where('agency_id', auth()->user()->agency_id)
                                  ->findOrFail($request->request_id);
                                  
        // التحقق من أن السبوكيل ينتمي للوكالة
        $subagent = User::where('agency_id', auth()->user()->agency_id)
                       ->where('user_type', 'subagent')
                       ->findOrFail($request->subagent_id);
        
        $quote = Quote::create([
            'request_id' => $serviceRequest->id,
            'subagent_id' => $subagent->id,
            'price' => $request->price,
            'commission_amount' => $request->commission_amount,
            'details' => $request->details,
            'status' => 'agency_approved', // عند إنشاء العرض من قبل الوكالة يكون معتمداً تلقائياً
        ]);
        
        return redirect()->route('agency.quotes.index')
                        ->with('success', 'تم إنشاء عرض السعر بنجاح.');
    }

    /**
     * عرض تفاصيل عرض سعر محدد
     */
    public function show(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للوكالة
        if ($quote->request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        return view('agency.quotes.show', compact('quote'));
    }

    /**
     * عرض نموذج تعديل عرض سعر
     */
    public function edit(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للوكالة
        if ($quote->request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        $subagents = User::where('agency_id', auth()->user()->agency_id)
                         ->where('user_type', 'subagent')
                         ->where('is_active', true)
                         ->get();
                         
        return view('agency.quotes.edit', compact('quote', 'subagents'));
    }

    /**
     * تحديث عرض سعر محدد
     */
    public function update(Request $request, Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للوكالة
        if ($quote->request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        $request->validate([
            'price' => 'required|numeric|min:0',
            'commission_amount' => 'required|numeric|min:0',
            'details' => 'required|string',
        ]);
        
        $quote->update([
            'price' => $request->price,
            'commission_amount' => $request->commission_amount,
            'details' => $request->details,
        ]);
        
        return redirect()->route('agency.quotes.index')
                        ->with('success', 'تم تحديث عرض السعر بنجاح.');
    }

    /**
     * حذف عرض سعر محدد
     */
    public function destroy(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للوكالة
        if ($quote->request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        $quote->delete();
        
        return redirect()->route('agency.quotes.index')
                        ->with('success', 'تم حذف عرض السعر بنجاح.');
    }

    /**
     * الموافقة على عرض سعر
     */
    public function approve(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للوكالة
        if ($quote->request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        // التحقق من أن العرض في حالة انتظار
        if ($quote->status !== 'pending') {
            return redirect()->back()->with('error', 'لا يمكن الموافقة على هذا العرض في حالته الحالية.');
        }
        
        $quote->update([
            'status' => 'agency_approved'
        ]);
        
        // يمكن هنا إضافة إشعار للسبوكيل بالموافقة على العرض
        
        return redirect()->back()->with('success', 'تمت الموافقة على عرض السعر بنجاح.');
    }

    /**
     * رفض عرض سعر
     */
    public function reject(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للوكالة
        if ($quote->request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        // التحقق من أن العرض في حالة انتظار
        if ($quote->status !== 'pending') {
            return redirect()->back()->with('error', 'لا يمكن رفض هذا العرض في حالته الحالية.');
        }
        
        $quote->update([
            'status' => 'agency_rejected'
        ]);
        
        // يمكن هنا إضافة إشعار للسبوكيل برفض العرض
        
        return redirect()->back()->with('success', 'تم رفض عرض السعر بنجاح.');
    }
}
