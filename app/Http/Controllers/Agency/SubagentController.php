<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SubagentController extends Controller
{
    /**
     * عرض قائمة السبوكلاء.
     */
    public function index(Request $request)
    {
        $query = User::where('agency_id', auth()->user()->agency_id)
                    ->where('user_type', 'subagent');

        // تطبيق عوامل التصفية
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $subagents = $query->latest()->paginate(10);

        return view('agency.subagents.index', compact('subagents'));
    }

    /**
     * عرض نموذج إنشاء سبوكيل جديد.
     */
    public function create()
    {
        $services = Service::where('agency_id', auth()->user()->agency_id)
                         ->where('status', 'active')
                         ->get();
                         
        return view('agency.subagents.create', compact('services'));
    }

    /**
     * تخزين سبوكيل جديد.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            'services' => ['nullable', 'array'],
        ]);

        $subagent = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => 'subagent',
            'agency_id' => auth()->user()->agency_id,
            'parent_id' => auth()->id(),
            'is_active' => true,
        ]);

        // ربط الخدمات مع السبوكيل
        if ($request->has('services') && is_array($request->services)) {
            foreach ($request->services as $serviceId) {
                $service = Service::findOrFail($serviceId);
                $subagent->services()->attach($service->id, [
                    'is_active' => true,
                    'custom_commission_rate' => $service->commission_rate,
                ]);
            }
        }

        return redirect()->route('agency.subagents.index')
                        ->with('success', 'تم إضافة السبوكيل بنجاح.');
    }

    /**
     * عرض تفاصيل السبوكيل.
     */
    public function show(User $subagent)
    {
        // التحقق من أن السبوكيل ينتمي لنفس الوكالة
        if ($subagent->agency_id !== auth()->user()->agency_id || $subagent->user_type !== 'subagent') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا السبوكيل');
        }

        return view('agency.subagents.show', compact('subagent'));
    }

    /**
     * عرض نموذج تعديل السبوكيل.
     */
    public function edit(User $subagent)
    {
        // التحقق من أن السبوكيل ينتمي لنفس الوكالة
        if ($subagent->agency_id !== auth()->user()->agency_id || $subagent->user_type !== 'subagent') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا السبوكيل');
        }

        $services = Service::where('agency_id', auth()->user()->agency_id)
                         ->where('status', 'active')
                         ->get();
                         
        return view('agency.subagents.edit', compact('subagent', 'services'));
    }

    /**
     * تحديث معلومات السبوكيل.
     */
    public function update(Request $request, User $subagent)
    {
        // التحقق من أن السبوكيل ينتمي لنفس الوكالة
        if ($subagent->agency_id !== auth()->user()->agency_id || $subagent->user_type !== 'subagent') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا السبوكيل');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($subagent->id)],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            'services' => ['nullable', 'array'],
        ]);

        $subagent->name = $request->name;
        $subagent->email = $request->email;
        $subagent->phone = $request->phone;
        
        if ($request->filled('password')) {
            $subagent->password = Hash::make($request->password);
        }
        
        $subagent->save();

        return redirect()->route('agency.subagents.index')
                        ->with('success', 'تم تحديث معلومات السبوكيل بنجاح.');
    }

    /**
     * حذف السبوكيل.
     */
    public function destroy(User $subagent)
    {
        // التحقق من أن السبوكيل ينتمي لنفس الوكالة
        if ($subagent->agency_id !== auth()->user()->agency_id || $subagent->user_type !== 'subagent') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا السبوكيل');
        }

        // حذف العلاقات أولاً
        $subagent->services()->detach();
        
        // حذف السبوكيل
        $subagent->delete();

        return redirect()->route('agency.subagents.index')
                        ->with('success', 'تم حذف السبوكيل بنجاح.');
    }

    /**
     * تغيير حالة السبوكيل (تفعيل/تعطيل).
     */
    public function toggleStatus(User $subagent)
    {
        // التحقق من أن السبوكيل ينتمي لنفس الوكالة
        if ($subagent->agency_id !== auth()->user()->agency_id || $subagent->user_type !== 'subagent') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا السبوكيل');
        }

        $subagent->is_active = !$subagent->is_active;
        $subagent->save();

        $status = $subagent->is_active ? 'تفعيل' : 'تعطيل';
        return redirect()->back()->with('success', "تم {$status} السبوكيل بنجاح.");
    }

    /**
     * تحديث الخدمات المتاحة للسبوكيل.
     */
    public function updateServices(Request $request, User $subagent)
    {
        // التحقق من أن السبوكيل ينتمي لنفس الوكالة
        if ($subagent->agency_id !== auth()->user()->agency_id || $subagent->user_type !== 'subagent') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا السبوكيل');
        }

        $request->validate([
            'services' => ['required', 'array'],
            'services.*.active' => ['sometimes', 'boolean'],
            'services.*.commission_rate' => ['sometimes', 'numeric', 'min:0', 'max:100'],
        ]);

        // فك ارتباط جميع الخدمات الحالية
        $subagent->services()->detach();

        // إضافة الخدمات المحددة مع العمولات المخصصة
        foreach ($request->services as $serviceId => $serviceData) {
            if (isset($serviceData['active']) && $serviceData['active']) {
                $commissionRate = $serviceData['commission_rate'] ?? 0;
                $subagent->services()->attach($serviceId, [
                    'is_active' => true,
                    'custom_commission_rate' => $commissionRate,
                ]);
            }
        }

        return redirect()->back()->with('success', 'تم تحديث خدمات السبوكيل بنجاح.');
    }
}
