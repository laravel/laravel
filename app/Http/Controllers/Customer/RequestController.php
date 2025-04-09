<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as ServiceRequest;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class RequestController extends Controller
{
    /**
     * عرض قائمة طلبات العميل.
     */
    public function index(Request $request)
    {

        // تم إزالة التخزين المؤقت لمنع مشكلة Serialization of 'Closure'
        $query = ServiceRequest::where('customer_id', auth()->id());

        // تحديد الحقول المطلوبة فقط لتحسين الأداء
        $query->select('id', 'service_id', 'customer_id', 'agency_id', 'status', 'priority', 'created_at');

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
                         ->select('id', 'name', 'type')
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
     * عرض تفاصيل طلب خدمة معين
     */
    public function show(ServiceRequest $request)
    {
        // تسجيل معلومات للتصحيح
        Log::info('Accessing request', [
            'request_id' => $request->id,
            'customer_id' => $request->customer_id,
            'auth_id' => auth()->id(),
            'user_type' => auth()->user()->user_type
        ]);

        // تعديل طريقة التحقق من أن الطلب ينتمي للعميل الحالي أو اي مستخدم لديه صلاحيات كافية
        if ($request->customer_id != auth()->id() && auth()->user()->user_type == 'customer') {
            // تسجيل محاولة الوصول غير المصرح بها للتصحيح
            Log::warning('Unauthorized access attempt', [
                'request_id' => $request->id, 
                'customer_id' => $request->customer_id, 
                'auth_id' => auth()->id()
            ]);
            
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }
        
        // تحميل العلاقات
        $request->load(['service', 'agency', 'quotes.subagent']);
        
        // تعريف حالات ونصوص عروض الأسعار
        $quoteStatusBadge = [
            'pending' => 'warning',
            'agency_approved' => 'info',
            'customer_approved' => 'success',
            'rejected' => 'danger',
            'expired' => 'secondary'
        ];
        
        $quoteStatusText = [
            'pending' => 'قيد المراجعة',
            'agency_approved' => 'معتمد من الوكالة',
            'customer_approved' => 'تم القبول',
            'rejected' => 'مرفوض',
            'expired' => 'منتهي الصلاحية'
        ];
        
        // تعريف حالات الطلب
        $statusBadge = $this->getStatusBadge($request->status);
        $statusText = $this->getStatusText($request->status);
        
        // تعريف ألوان ونصوص سجل التحديثات
        $statusColors = [
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];
        
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي'
        ];
        
        return view('customer.requests.show', compact(
            'request', 
            'quoteStatusBadge', 
            'quoteStatusText', 
            'statusBadge', 
            'statusText',
            'statusColors',
            'statusLabels'
        ));
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
     * إلغاء طلب خدمة
     */
    public function cancel(ServiceRequest $request)
    {
        // التحقق من أن الطلب ينتمي للعميل الحالي
        if ($request->customer_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بإلغاء هذا الطلب');
        }

        // التحقق من حالة الطلب - لا يمكن إلغاء الطلبات المكتملة أو الملغية
        if (in_array($request->status, ['completed', 'cancelled'])) {
            return redirect()->route('customer.requests.show', $request)->with('error', 'لا يمكن إلغاء هذا الطلب في وضعه الحالي');
        }

        // تحديث حالة الطلب
        $request->status = 'cancelled';
        $request->save();

        // إرسال إشعارات للسبوكلاء ومدير الوكالة (يمكن إضافة هذه الوظيفة لاحقاً)
        
        return redirect()->route('customer.requests.index')->with('success', 'تم إلغاء الطلب بنجاح');
    }
}
