<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\Service;
use App\Models\User;
use App\Models\Request as ServiceRequest;
use App\Models\QuoteAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Notification;
use App\Notifications\QuoteStatusChanged;

class QuoteController extends Controller
{
    /**
     * عرض قائمة عروض الأسعار للوكالة
     */
    public function index(Request $request)
    {
        $query = Quote::whereHas('request', function($q) {
            $q->where('agency_id', Auth::user()->agency_id);
        });

        // تطبيق عوامل التصفية
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('subagent_id') && !empty($request->subagent_id)) {
            $query->where('subagent_id', $request->subagent_id);
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
        $quotes = $query->with(['request.service', 'request.customer', 'subagent'])
                       ->latest()
                       ->paginate(15);

        // الحصول على السبوكلاء التابعين للوكالة
        $subagents = User::where('agency_id', Auth::user()->agency_id)
                                   ->where('user_type', 'subagent')
                                   ->get();

        // الحصول على الطلبات التابعة للوكالة
        $requests = ServiceRequest::where('agency_id', Auth::user()->agency_id)
                                     ->get();
                                     
        // الحصول على الخدمات التابعة للوكالة
        $services = Service::where('agency_id', Auth::user()->agency_id)->get();

        return view('agency.quotes.index', compact('quotes', 'subagents', 'requests', 'services'));
    }

    /**
     * عرض نموذج إنشاء عرض سعر
     */
    public function create(Request $request)
    {
        $request_id = $request->query('request_id');
        $serviceRequest = null;
        
        if ($request_id) {
            $serviceRequest = ServiceRequest::where('agency_id', Auth::user()->agency_id)
                                      ->findOrFail($request_id);
        }
        
        $requests = ServiceRequest::where('agency_id', Auth::user()->agency_id)
                                 ->orderBy('created_at', 'desc')
                                 ->get();
                                 
        $subagents = User::where('agency_id', Auth::user()->agency_id)
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
        $serviceRequest = ServiceRequest::where('agency_id', Auth::user()->agency_id)
                                  ->findOrFail($request->request_id);
                                  
        // التحقق من أن السبوكيل ينتمي للوكالة
        $subagent = User::where('agency_id', Auth::user()->agency_id)
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
     * عرض تفاصيل عرض سعر معين
     */
    public function show(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للوكالة
        if ($quote->request->agency_id !== Auth::user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }

        $quote->load(['request.service', 'request.customer', 'subagent']);
        
        // تحميل المرفقات إذا كان الجدول موجودًا
        if (Schema::hasTable('quote_attachments')) {
            $quote->load('attachments');
        }

        return view('agency.quotes.show', compact('quote'));
    }

    /**
     * عرض نموذج تعديل عرض سعر
     */
    public function edit(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للوكالة
        if ($quote->request->agency_id !== Auth::user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        $subagents = User::where('agency_id', Auth::user()->agency_id)
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
        if ($quote->request->agency_id !== Auth::user()->agency_id) {
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
        if ($quote->request->agency_id !== Auth::user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        $quote->delete();
        
        return redirect()->route('agency.quotes.index')
                        ->with('success', 'تم حذف عرض السعر بنجاح.');
    }

    /**
     * الموافقة على عرض سعر
     */
    public function approve(Request $request, Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للوكالة
        if ($quote->request->agency_id !== Auth::user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }

        // التحقق من أن حالة عرض السعر تسمح بالموافقة
        if ($quote->status !== 'pending') {
            return redirect()->route('agency.quotes.show', $quote)
                           ->with('error', 'لا يمكن الموافقة على هذا العرض في حالته الحالية');
        }

        // تحديث حالة عرض السعر
        $quote->update([
            'status' => 'agency_approved',
        ]);

        // إرسال إشعار للسبوكيل والعميل
        try {
            $quote->subagent->notify(new QuoteStatusChanged($quote));
            $quote->request->customer->notify(new QuoteStatusChanged($quote));
        } catch (\Exception $e) {
            // تسجيل الخطأ فقط، لا نريد إيقاف العملية
            \Log::error('Error sending notification: ' . $e->getMessage());
        }

        return redirect()->route('agency.quotes.show', $quote)
                        ->with('success', 'تمت الموافقة على عرض السعر بنجاح وإخطار السبوكيل والعميل');
    }

    /**
     * رفض عرض سعر
     */
    public function reject(Request $request, Quote $quote)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // التحقق من أن عرض السعر ينتمي للوكالة
        if ($quote->request->agency_id !== Auth::user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }

        // التحقق من أن حالة عرض السعر تسمح بالرفض
        if ($quote->status !== 'pending') {
            return redirect()->route('agency.quotes.show', $quote)
                           ->with('error', 'لا يمكن رفض هذا العرض في حالته الحالية');
        }

        // تحديث حالة عرض السعر
        $quote->update([
            'status' => 'agency_rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // إرسال إشعار للسبوكيل
        try {
            $quote->subagent->notify(new QuoteStatusChanged($quote));
        } catch (\Exception $e) {
            // تسجيل الخطأ فقط
            \Log::error('Error sending notification: ' . $e->getMessage());
        }

        return redirect()->route('agency.quotes.show', $quote)
                        ->with('success', 'تم رفض عرض السعر وإخطار السبوكيل');
    }
}
