<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as ServiceRequest;
use App\Models\Service;

class RequestController extends Controller
{
    /**
     * عرض قائمة طلبات العميل.
     */
    public function index(Request $request)
    {
        $query = ServiceRequest::where('customer_id', auth()->id());

        // تطبيق عوامل التصفية
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('service_id') && !empty($request->service_id)) {
            $query->where('service_id', $request->service_id);
        }

        // ترتيب وتصنيف النتائج
        $requests = $query->latest()->paginate(10);
        
        // الحصول على الخدمات للتصفية
        $services = Service::where('agency_id', auth()->user()->agency_id)
                         ->where('status', 'active')
                         ->get();
                         
        return view('customer.requests.index', compact('requests', 'services'));
    }

    /**
     * عرض نموذج إنشاء طلب جديد.
     */
    public function create()
    {
        $services = Service::where('agency_id', auth()->user()->agency_id)
                         ->where('status', 'active')
                         ->get();
                         
        return view('customer.requests.create', compact('services'));
    }

    /**
     * تخزين طلب جديد.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'details' => 'required|string',
            'requested_date' => 'nullable|date|after_or_equal:today',
        ]);

        // التحقق من أن الخدمة تنتمي لنفس وكالة العميل وأنها نشطة
        $service = Service::where('agency_id', auth()->user()->agency_id)
                        ->where('status', 'active')
                        ->findOrFail($request->service_id);
        
        $serviceRequest = ServiceRequest::create([
            'service_id' => $service->id,
            'customer_id' => auth()->id(),
            'agency_id' => auth()->user()->agency_id,
            'details' => $request->details,
            'priority' => $request->input('priority', 'normal'),
            'status' => 'pending',
            'requested_date' => $request->requested_date,
        ]);

        // إرفاق المستندات إذا وجدت
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                $serviceRequest->documents()->create([
                    'name' => $request->document_names[$index] ?? 'مستند ' . ($index + 1),
                    'file_path' => $file->store('request_documents', 'public'),
                    'file_type' => $file->getClientOriginalExtension(),
                    'visibility' => $request->document_visibility[$index] ?? 'public',
                ]);
            }
        }

        return redirect()->route('customer.requests.index')
                        ->with('success', 'تم إنشاء الطلب بنجاح.');
    }

    /**
     * عرض تفاصيل طلب معين.
     */
    public function show(ServiceRequest $request)
    {
        // التحقق من أن الطلب ينتمي للعميل
        if ($request->customer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }
        
        return view('customer.requests.show', compact('request'));
    }

    /**
     * إلغاء طلب.
     */
    public function cancel(ServiceRequest $request)
    {
        // التحقق من أن الطلب ينتمي للعميل
        if ($request->customer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }
        
        // التحقق من أن الطلب في حالة قابلة للإلغاء
        if ($request->status !== 'pending' && $request->status !== 'in_progress') {
            return redirect()->back()->with('error', 'لا يمكن إلغاء هذا الطلب في وضعه الحالي.');
        }
        
        $request->update([
            'status' => 'cancelled'
        ]);
        
        return redirect()->back()->with('success', 'تم إلغاء الطلب بنجاح.');
    }
}
