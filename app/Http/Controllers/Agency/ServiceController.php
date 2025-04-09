<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Helpers\ServiceTypeHelper;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات
     */
    public function index()
    {
        $services = Service::where('agency_id', Auth::user()->agency_id)
                         ->latest()
                         ->paginate(15);
        
        return view('agency.services.index', compact('services'));
    }

    /**
     * عرض نموذج إنشاء خدمة جديدة
     */
    public function create()
    {
        $serviceTypes = ServiceTypeHelper::getTypes();
        $subagents = \App\Models\User::where('agency_id', Auth::user()->agency_id)
                                   ->where('user_type', 'subagent')
                                   ->get();
        $currencies = \App\Models\Currency::where('is_active', true)->get();
        
        return view('agency.services.create', compact('serviceTypes', 'subagents', 'currencies'));
    }

    /**
     * تخزين خدمة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'currency_code' => 'required|exists:currencies,code',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'subagents' => 'nullable|array',
            'subagents.*' => 'exists:users,id',
        ]);

        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'agency_id' => Auth::user()->agency_id,
            'type' => $request->type,
            'base_price' => $request->base_price,
            'currency_code' => $request->currency_code,
            'commission_rate' => $request->commission_rate,
            'status' => $request->status,
        ]);

        // إرفاق السبوكلاء إذا تم تحديدهم
        if ($request->has('subagents')) {
            foreach ($request->subagents as $subagentId) {
                $service->subagents()->attach($subagentId, ['is_active' => true]);
            }
        }

        return redirect()->route('agency.services.index')
                        ->with('success', 'تم إنشاء الخدمة بنجاح');
    }

    /**
     * عرض تفاصيل خدمة معينة
     */
    public function show(Service $service)
    {
        // التحقق من أن الخدمة تنتمي للوكالة
        if ($service->agency_id !== Auth::user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخدمة');
        }

        $service->load('subagents');
        
        return view('agency.services.show', compact('service'));
    }

    /**
     * عرض نموذج تعديل خدمة
     */
    public function edit(Service $service)
    {
        // التحقق من أن الخدمة تنتمي للوكالة
        if ($service->agency_id !== Auth::user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخدمة');
        }

        $serviceTypes = ServiceTypeHelper::getTypes();
        $subagents = \App\Models\User::where('agency_id', Auth::user()->agency_id)
                                   ->where('user_type', 'subagent')
                                   ->get();
        $currencies = \App\Models\Currency::where('is_active', true)->get();
        
        $selectedSubagents = $service->subagents->pluck('id')->toArray();
        
        return view('agency.services.edit', compact('service', 'serviceTypes', 'subagents', 'selectedSubagents', 'currencies'));
    }

    /**
     * تحديث خدمة معينة
     */
    public function update(Request $request, Service $service)
    {
        // التحقق من أن الخدمة تنتمي للوكالة
        if ($service->agency_id !== Auth::user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخدمة');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'currency_code' => 'required|exists:currencies,code',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'subagents' => 'nullable|array',
            'subagents.*' => 'exists:users,id',
        ]);

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'base_price' => $request->base_price,
            'currency_code' => $request->currency_code,
            'commission_rate' => $request->commission_rate,
            'status' => $request->status,
        ]);

        // تحديث السبوكلاء
        $service->subagents()->detach();
        if ($request->has('subagents')) {
            foreach ($request->subagents as $subagentId) {
                $service->subagents()->attach($subagentId, ['is_active' => true]);
            }
        }

        return redirect()->route('agency.services.show', $service)
                        ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * حذف خدمة معينة
     */
    public function destroy(Service $service)
    {
        // التحقق من أن الخدمة تنتمي للوكالة
        if ($service->agency_id !== Auth::user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخدمة');
        }

        // التحقق من عدم وجود طلبات مرتبطة بالخدمة
        $hasRequests = \App\Models\Request::where('service_id', $service->id)->exists();
        if ($hasRequests) {
            return redirect()->route('agency.services.index')
                           ->with('error', 'لا يمكن حذف الخدمة لوجود طلبات مرتبطة بها');
        }

        $service->subagents()->detach();
        $service->delete();

        return redirect()->route('agency.services.index')
                        ->with('success', 'تم حذف الخدمة بنجاح');
    }
}
