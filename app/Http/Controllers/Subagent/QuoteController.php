<?php

namespace App\Http\Controllers\Subagent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\Request as ServiceRequest;
use App\Models\QuoteAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

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
     * عرض نموذج إنشاء عرض سعر جديد.
     */
    public function create(ServiceRequest $request)
    {
        return view('subagent.quotes.create', compact('request'));
    }

    /**
     * تخزين عرض سعر جديد.
     */
    public function store(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:requests,id',
            'price' => 'required|numeric|min:0',
            'details' => 'required|string',
            'currency_code' => 'required|string|exists:currencies,code',
        ]);

        $quote = Quote::create([
            'subagent_id' => Auth::id(),
            'request_id' => $request->request_id,
            'price' => $request->price,
            'details' => $request->details,
            'currency_code' => $request->currency_code,
            'status' => 'pending',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $index => $file) {
                $quote->attachments()->create([
                    'name' => $request->attachment_names[$index] ?? 'مرفق ' . ($index + 1),
                    'file_path' => $file->store('quote_attachments', 'public'),
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        return redirect()->route('subagent.quotes.index')
                        ->with('success', 'تم إنشاء عرض السعر بنجاح');
    }

    /**
     * عرض تفاصيل عرض سعر معين.
     */
    public function show(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للسبوكيل الحالي
        if ($quote->subagent_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العرض');
        }
        
        // تحميل البيانات المرتبطة
        $quote->load(['request.service', 'request.customer']);
        
        // تحميل المرفقات إذا كان الجدول موجودًا
        if (Schema::hasTable('quote_attachments')) {
            $quote->load('attachments');
        }
        
        return view('subagent.quotes.show', compact('quote'));
    }

    /**
     * عرض نموذج تعديل عرض سعر.
     */
    public function edit(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للسبوكيل الحالي
        if ($quote->subagent_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا العرض');
        }

        // التحقق من أن حالة عرض السعر تسمح بالتعديل
        if (!in_array($quote->status, ['pending', 'agency_rejected'])) {
            return redirect()->route('subagent.quotes.show', $quote)
                            ->with('error', 'لا يمكن تعديل هذا العرض في حالته الحالية');
        }

        // تحميل البيانات المرتبطة
        $quote->load(['request.service', 'request.customer']);
        
        // تحميل المرفقات إذا كان الجدول موجودًا
        if (Schema::hasTable('quote_attachments')) {
            $quote->load('attachments');
        }
        
        return view('subagent.quotes.edit', compact('quote'));
    }

    /**
     * تحديث عرض سعر معين.
     */
    public function update(Request $request, Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للسبوكيل الحالي
        if ($quote->subagent_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا العرض');
        }

        // التحقق من أن حالة عرض السعر تسمح بالتعديل
        if (!in_array($quote->status, ['pending', 'agency_rejected'])) {
            return redirect()->route('subagent.quotes.show', $quote)
                            ->with('error', 'لا يمكن تعديل هذا العرض في حالته الحالية');
        }

        // التحقق من البيانات
        $request->validate([
            'price' => 'required|numeric|min:0',
            'details' => 'required|string',
            'currency_code' => 'required|string|exists:currencies,code',
        ]);

        // حساب العمولة
        $commissionRate = 10; // قيمة افتراضية
        if (Auth::user()->commission_rate) {
            $commissionRate = Auth::user()->commission_rate;
        }
        
        $commissionAmount = ($request->price * $commissionRate) / 100;

        // تحديث عرض السعر
        $quote->update([
            'price' => $request->price,
            'details' => $request->details,
            'currency_code' => $request->currency_code,
            'commission_amount' => $commissionAmount,
            'status' => 'pending', // إعادة الحالة إلى "قيد المراجعة" بعد التعديل
        ]);

        // إضافة المرفقات الجديدة إذا وجدت والجدول موجود
        if (Schema::hasTable('quote_attachments') && $request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $index => $file) {
                if ($file->isValid()) {
                    $quote->attachments()->create([
                        'name' => $request->attachment_names[$index] ?? 'مرفق ' . ($index + 1),
                        'file_path' => $file->store('quote_attachments', 'public'),
                        'file_type' => $file->getClientOriginalExtension(),
                    ]);
                }
            }
        }

        return redirect()->route('subagent.quotes.show', $quote)
                        ->with('success', 'تم تحديث عرض السعر بنجاح');
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

    /**
     * حذف عرض سعر.
     */
    public function destroy(Quote $quote)
    {
        // التحقق من أن عرض السعر ينتمي للسبوكيل الحالي
        if ($quote->subagent_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بحذف هذا العرض');
        }

        // التحقق من أن حالة عرض السعر تسمح بالحذف
        if (!in_array($quote->status, ['pending', 'agency_rejected'])) {
            return redirect()->route('subagent.quotes.index')
                            ->with('error', 'لا يمكن حذف هذا العرض في حالته الحالية');
        }

        // حذف المرفقات إذا كان الجدول موجودًا
        if (Schema::hasTable('quote_attachments') && $quote->attachments->count() > 0) {
            foreach ($quote->attachments as $attachment) {
                // حذف الملف من التخزين
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
            }
        }

        // حذف عرض السعر
        $quote->delete();

        return redirect()->route('subagent.quotes.index')
                        ->with('success', 'تم حذف عرض السعر بنجاح');
    }
}
