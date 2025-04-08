<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as ServiceRequest;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    /**
     * Display a listing of the requests.
     */
    public function index(Request $request)
    {
        $query = ServiceRequest::where('agency_id', auth()->user()->agency_id);

        // Aplicar filtros de búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('id', 'like', '%' . $request->search . '%')
                  ->orWhere('details', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('service') && !empty($request->service)) {
            $query->where('service_id', $request->service);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && !empty($request->priority)) {
            $query->where('priority', $request->priority);
        }

        // Ordenar y paginar resultados
        $requests = $query->latest()->paginate(10);
        
        // Obtener servicios para el filtro
        $services = Service::where('agency_id', auth()->user()->agency_id)->get();

        return view('agency.requests.index', compact('requests', 'services'));
    }

    /**
     * Show the form for creating a new request.
     */
    public function create()
    {
        $services = Service::where('agency_id', auth()->user()->agency_id)
                          ->where('status', 'active')
                          ->get();
        
        $customers = User::where('agency_id', auth()->user()->agency_id)
                         ->where('user_type', 'customer')
                         ->where('is_active', true)
                         ->get();
                         
        return view('agency.requests.create', compact('services', 'customers'));
    }

    /**
     * Store a newly created request in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'customer_id' => 'required|exists:users,id',
            'details' => 'required|string',
            'priority' => 'required|in:normal,urgent,emergency',
            'requested_date' => 'nullable|date|after_or_equal:today',
        ]);

        // Verificar que el servicio pertenece a la agencia
        $service = Service::where('agency_id', auth()->user()->agency_id)
                        ->findOrFail($request->service_id);
        
        // Verificar que el cliente pertenece a la agencia
        $customer = User::where('agency_id', auth()->user()->agency_id)
                       ->where('user_type', 'customer')
                       ->findOrFail($request->customer_id);
        
        $serviceRequest = ServiceRequest::create([
            'service_id' => $service->id,
            'customer_id' => $customer->id,
            'agency_id' => auth()->user()->agency_id,
            'details' => $request->details,
            'priority' => $request->priority,
            'status' => 'pending',
            'requested_date' => $request->requested_date,
        ]);

        // Manejar documentos si se adjuntan
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                $serviceRequest->documents()->create([
                    'name' => $request->document_names[$index] ?? 'Documento ' . ($index + 1),
                    'file_path' => $file->store('request_documents', 'public'),
                    'file_type' => $file->getClientOriginalExtension(),
                    'visibility' => $request->document_visibility[$index] ?? 'private',
                ]);
            }
        }

        return redirect()->route('agency.requests.index')
                        ->with('success', 'تم إنشاء الطلب بنجاح.');
    }

    /**
     * Display the specified request.
     */
    public function show(ServiceRequest $request)
    {
        // Verificar que la solicitud pertenece a la agencia
        if ($request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }
        
        return view('agency.requests.show', compact('request'));
    }

    /**
     * Show the form for editing the specified request.
     */
    public function edit(ServiceRequest $request)
    {
        // Verificar que la solicitud pertenece a la agencia
        if ($request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }
        
        $services = Service::where('agency_id', auth()->user()->agency_id)
                          ->where('status', 'active')
                          ->get();
        
        $customers = User::where('agency_id', auth()->user()->agency_id)
                         ->where('user_type', 'customer')
                         ->where('is_active', true)
                         ->get();
                         
        return view('agency.requests.edit', compact('request', 'services', 'customers'));
    }

    /**
     * Update the specified request in storage.
     */
    public function update(Request $httpRequest, ServiceRequest $request)
    {
        // Verificar que la solicitud pertenece a la agencia
        if ($request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }
        
        $httpRequest->validate([
            'service_id' => 'required|exists:services,id',
            'customer_id' => 'required|exists:users,id',
            'details' => 'required|string',
            'priority' => 'required|in:normal,urgent,emergency',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'requested_date' => 'nullable|date',
        ]);

        // Verificar que el servicio pertenece a la agencia
        $service = Service::where('agency_id', auth()->user()->agency_id)
                        ->findOrFail($httpRequest->service_id);
        
        // Verificar que el cliente pertenece a la agencia
        $customer = User::where('agency_id', auth()->user()->agency_id)
                       ->where('user_type', 'customer')
                       ->findOrFail($httpRequest->customer_id);
        
        $request->update([
            'service_id' => $service->id,
            'customer_id' => $customer->id,
            'details' => $httpRequest->details,
            'priority' => $httpRequest->priority,
            'status' => $httpRequest->status,
            'requested_date' => $httpRequest->requested_date,
        ]);

        return redirect()->route('agency.requests.index')
                        ->with('success', 'تم تحديث الطلب بنجاح.');
    }

    /**
     * Remove the specified request from storage.
     */
    public function destroy(ServiceRequest $request)
    {
        // Verificar que la solicitud pertenece a la agencia
        if ($request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }

        // Eliminar documentos asociados primero
        foreach ($request->documents as $document) {
            Storage::disk('public')->delete($document->file_path);
            $document->delete();
        }
        
        // Eliminar la solicitud
        $request->delete();

        return redirect()->route('agency.requests.index')
                        ->with('success', 'تم حذف الطلب بنجاح.');
    }

    /**
     * Update the status of the specified request.
     */
    public function updateStatus(Request $httpRequest, ServiceRequest $request)
    {
        // Verificar que la solicitud pertenece a la agencia
        if ($request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }
        
        $httpRequest->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);
        
        $request->update([
            'status' => $httpRequest->status,
        ]);
        
        return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }

    /**
     * Share the request with specific subagents.
     */
    public function shareWithSubagents(Request $httpRequest, ServiceRequest $request)
    {
        // Verificar que la solicitud pertenece a la agencia
        if ($request->agency_id !== auth()->user()->agency_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }
        
        $httpRequest->validate([
            'subagents' => 'required|array',
            'subagents.*' => 'exists:users,id',
            'message' => 'nullable|string',
        ]);
        
        // Verificar que los subagentes pertenecen a la agencia
        $subagents = User::where('agency_id', auth()->user()->agency_id)
                        ->where('user_type', 'subagent')
                        ->whereIn('id', $httpRequest->subagents)
                        ->get();
                        
        if ($subagents->count() != count($httpRequest->subagents)) {
            return redirect()->back()->with('error', 'بعض السبوكلاء المحددين غير صالحين.');
        }
        
        // Aquí implementaríamos la lógica para compartir la solicitud con los subagentes
        // Por ejemplo, enviando notificaciones o creando registros en una tabla de compartidos
        
        // Simplemente como demostración, podemos registrar un evento de actividad
        foreach ($subagents as $subagent) {
            // Implementar lógica de compartir como sea necesario
            // Por ejemplo: ActivityLog::create(['user_id' => auth()->id(), 'subject_id' => $request->id, 'action' => 'shared', 'recipient_id' => $subagent->id]);
        }
        
        return redirect()->back()->with('success', 'تم مشاركة الطلب مع السبوكلاء المحددين بنجاح.');
    }
}
