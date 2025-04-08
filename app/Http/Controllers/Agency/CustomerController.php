<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = User::where('agency_id', auth()->user()->agency_id)
                    ->where('user_type', 'customer');

        // Aplicar filtros
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

        // Ordenar y paginar
        $customers = $query->latest()->paginate(10);

        return view('agency.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('agency.customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => 'customer',
            'agency_id' => auth()->user()->agency_id,
            'parent_id' => auth()->id(),
            'is_active' => true,
        ]);

        return redirect()->route('agency.customers.index')
                        ->with('success', 'تم إضافة العميل بنجاح.');
    }

    /**
     * Display the specified customer.
     */
    public function show(User $customer)
    {
        // Verificar que el cliente pertenece a la agencia
        if ($customer->agency_id !== auth()->user()->agency_id || $customer->user_type !== 'customer') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العميل');
        }

        return view('agency.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(User $customer)
    {
        // Verificar que el cliente pertenece a la agencia
        if ($customer->agency_id !== auth()->user()->agency_id || $customer->user_type !== 'customer') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العميل');
        }

        return view('agency.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, User $customer)
    {
        // Verificar que el cliente pertenece a la agencia
        if ($customer->agency_id !== auth()->user()->agency_id || $customer->user_type !== 'customer') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العميل');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($customer->id)],
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8',
        ]);

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        
        if ($request->filled('password')) {
            $customer->password = Hash::make($request->password);
        }
        
        $customer->save();

        return redirect()->route('agency.customers.index')
                        ->with('success', 'تم تحديث معلومات العميل بنجاح.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(User $customer)
    {
        // Verificar que el cliente pertenece a la agencia
        if ($customer->agency_id !== auth()->user()->agency_id || $customer->user_type !== 'customer') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العميل');
        }

        // Verificar si el cliente tiene solicitudes
        if ($customer->customerRequests()->count() > 0) {
            return redirect()->route('agency.customers.index')
                            ->with('error', 'لا يمكن حذف العميل لارتباطه بطلبات.');
        }

        $customer->delete();

        return redirect()->route('agency.customers.index')
                        ->with('success', 'تم حذف العميل بنجاح.');
    }

    /**
     * Toggle customer status (active/inactive).
     */
    public function toggleStatus(User $customer)
    {
        // Verificar que el cliente pertenece a la agencia
        if ($customer->agency_id !== auth()->user()->agency_id || $customer->user_type !== 'customer') {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العميل');
        }

        $customer->is_active = !$customer->is_active;
        $customer->save();

        $status = $customer->is_active ? 'تفعيل' : 'تعطيل';
        return redirect()->back()->with('success', "تم {$status} العميل بنجاح.");
    }
}
