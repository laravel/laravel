<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index(Request $request)
    {
        $query = Service::where('agency_id', auth()->user()->agency_id);

        // Aplicar filtros
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Ordenar y paginar
        $services = $query->latest()->paginate(10);

        return view('agency.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('agency.services.create');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:security_approval,transportation,hajj_umrah,flight,passport,other',
            'base_price' => 'required|numeric|min:0',
            'currency_code' => 'required|exists:currencies,code',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('image');
        $data['agency_id'] = auth()->user()->agency_id;

        // تحميل الصورة إذا وجدت
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('services', 'public');
        }

        Service::create($data);

        return redirect()->route('agency.services.index')
                         ->with('success', 'تم إنشاء الخدمة بنجاح');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        // Verificar que el servicio pertenece a la agencia
        if ($service->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخدمة');
        }

        return view('agency.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        // Verificar que el servicio pertenece a la agencia
        if ($service->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخدمة');
        }

        return view('agency.services.edit', compact('service'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        // Verificar que el servicio pertenece a la agencia
        if ($service->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخدمة');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:security_approval,transportation,hajj_umrah,flight,passport,other',
            'base_price' => 'required|numeric|min:0',
            'currency_code' => 'required|exists:currencies,code',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('image');

        // تحميل الصورة الجديدة إذا وجدت
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return redirect()->route('agency.services.index')
                         ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        // Verificar que el servicio pertenece a la agencia
        if ($service->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخدمة');
        }

        // Check if service has associated requests or subagents
        if ($service->requests()->count() > 0) {
            return redirect()->route('agency.services.index')
                            ->with('error', 'لا يمكن حذف الخدمة لارتباطها بطلبات.');
        }

        // Delete image if exists
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }

        // Detach all subagents
        $service->subagents()->detach();

        // Delete service
        $service->delete();

        return redirect()->route('agency.services.index')
                        ->with('success', 'تم حذف الخدمة بنجاح.');
    }

    /**
     * Toggle service status (active/inactive).
     */
    public function toggleStatus(Service $service)
    {
        // Verificar que el servicio pertenece a la agencia
        if ($service->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخدمة');
        }

        $service->status = $service->status === 'active' ? 'inactive' : 'active';
        $service->save();

        $status = $service->status === 'active' ? 'تفعيل' : 'تعطيل';
        return redirect()->back()->with('success', "تم {$status} الخدمة بنجاح.");
    }
}
