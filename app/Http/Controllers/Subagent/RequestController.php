<?php

namespace App\Http\Controllers\Subagent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as ServiceRequest;
use App\Models\Quote;

class RequestController extends Controller
{
    /**
     * عرض قائمة الطلبات المتاحة للسبوكيل.
     */
    public function index(Request $request)
    {
        // الطلبات المتاحة للسبوكيل: طلبات مرتبطة بخدمات متاحة للسبوكيل ولم يقدم لها عرض سعر بعد
        $query = ServiceRequest::whereHas('service', function($query) {
                                  $query->whereHas('subagents', function($q) {
                                      $q->where('users.id', auth()->id())
                                        ->where('service_subagent.is_active', true);
                                  });
                              })
                              ->where('status', 'pending')
                              ->whereDoesntHave('quotes', function($query) {
                                  $query->where('subagent_id', auth()->id());
                              });
        
        // تطبيق عوامل التصفية
        if ($request->has('service_id') && !empty($request->service_id)) {
            $query->where('service_id', $request->service_id);
        }
        
        // ترتيب وتصنيف النتائج
        $requests = $query->with(['service', 'customer'])
                         ->latest()
                         ->paginate(10);
        
        // الحصول على الخدمات المتاحة للسبوكيل للتصفية
        $services = auth()->user()->services()
                          ->where('status', 'active')
                          ->where('service_subagent.is_active', true)
                          ->get();
                         
        return view('subagent.requests.index', compact('requests', 'services'));
    }

    /**
     * عرض تفاصيل طلب معين.
     */
    public function show(ServiceRequest $request)
    {
        // التحقق من أن الطلب متاح للسبوكيل
        $serviceAvailable = $request->service->subagents()
                                   ->where('users.id', auth()->id())
                                   ->where('service_subagent.is_active', true)
                                   ->exists();
        
        if (!$serviceAvailable || $request->status !== 'pending') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }
        
        // التحقق مما إذا كان السبوكيل قد قدم عرض سعر لهذا الطلب بالفعل
        $existingQuote = Quote::where('request_id', $request->id)
                            ->where('subagent_id', auth()->id())
                            ->first();
        
        return view('subagent.requests.show', compact('request', 'existingQuote'));
    }

    /**
     * عرض نموذج تقديم عرض سعر لطلب معين.
     */
    public function createQuote(ServiceRequest $request)
    {
        // التحقق من أن الطلب متاح للسبوكيل
        $serviceAvailable = $request->service->subagents()
                                   ->where('users.id', auth()->id())
                                   ->where('service_subagent.is_active', true)
                                   ->exists();
        
        if (!$serviceAvailable || $request->status !== 'pending') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }
        
        // التحقق مما إذا كان السبوكيل قد قدم عرض سعر لهذا الطلب بالفعل
        $existingQuote = Quote::where('request_id', $request->id)
                            ->where('subagent_id', auth()->id())
                            ->first();
        
        if ($existingQuote) {
            return redirect()->route('subagent.quotes.edit', $existingQuote)
                            ->with('info', 'لقد قمت بتقديم عرض سعر لهذا الطلب مسبقاً. يمكنك تعديله من هنا.');
        }
        
        // الحصول على معدل العمولة المخصص للسبوكيل على هذه الخدمة
        $serviceSubagent = $request->service->subagents()
                                  ->where('users.id', auth()->id())
                                  ->first();
        
        $commissionRate = $serviceSubagent ? $serviceSubagent->pivot->custom_commission_rate : $request->service->commission_rate;
        
        return view('subagent.requests.create_quote', compact('request', 'commissionRate'));
    }

    /**
     * تخزين عرض سعر جديد.
     */
    public function storeQuote(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'details' => 'required|string',
        ]);
        
        // التحقق من أن الطلب متاح للسبوكيل
        $serviceAvailable = $serviceRequest->service->subagents()
                                   ->where('users.id', auth()->id())
                                   ->where('service_subagent.is_active', true)
                                   ->exists();
        
        if (!$serviceAvailable || $serviceRequest->status !== 'pending') {
            abort(403, 'غير مصرح لك بتقديم عرض سعر لهذا الطلب');
        }
        
        // التحقق مما إذا كان السبوكيل قد قدم عرض سعر لهذا الطلب بالفعل
        $existingQuote = Quote::where('request_id', $serviceRequest->id)
                            ->where('subagent_id', auth()->id())
                            ->first();
        
        if ($existingQuote) {
            return redirect()->route('subagent.quotes.edit', $existingQuote)
                            ->with('info', 'لقد قمت بتقديم عرض سعر لهذا الطلب مسبقاً. يمكنك تعديله من هنا.');
        }
        
        // الحصول على معدل العمولة المخصص للسبوكيل على هذه الخدمة
        $serviceSubagent = $serviceRequest->service->subagents()
                                  ->where('users.id', auth()->id())
                                  ->first();
        
        $commissionRate = $serviceSubagent ? $serviceSubagent->pivot->custom_commission_rate : $serviceRequest->service->commission_rate;
        
        // حساب مبلغ العمولة
        $commissionAmount = $request->price * ($commissionRate / 100);
        
        // إنشاء عرض السعر
        $quote = Quote::create([
            'request_id' => $serviceRequest->id,
            'subagent_id' => auth()->id(),
            'price' => $request->price,
            'details' => $request->details,
            'status' => 'pending',
            'commission_amount' => $commissionAmount,
            'commission_rate' => $commissionRate,
        ]);
        
        return redirect()->route('subagent.quotes.index')
                        ->with('success', 'تم تقديم عرض السعر بنجاح. بانتظار مراجعته من قبل الوكالة.');
    }
}
