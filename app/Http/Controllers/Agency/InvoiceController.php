<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Request as ServiceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * عرض قائمة الفواتير
     */
    public function index(Request $request)
    {
        $query = Invoice::query()->where('agency_id', auth()->user()->agency_id);
        
        // التصفية حسب الحالة
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // التصفية حسب العميل
        if ($request->has('customer_id') && !empty($request->customer_id)) {
            $query->where('user_id', $request->customer_id);
        }
        
        // التصفية حسب التاريخ
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $invoices = $query->with(['user', 'request'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(15);
        
        return view('agency.invoices.index', compact('invoices'));
    }
    
    /**
     * عرض نموذج إنشاء فاتورة جديدة
     */
    public function create(Request $request)
    {
        $serviceRequest = null;
        if ($request->has('request_id')) {
            $serviceRequest = ServiceRequest::findOrFail($request->request_id);
        }
        
        $customers = \App\Models\User::where('user_type', 'customer')
                                    ->where('agency_id', auth()->user()->agency_id)
                                    ->get();
                                    
        $services = \App\Models\Service::where('agency_id', auth()->user()->agency_id)
                                     ->where('status', 'active')
                                     ->get();
                                     
        $currencies = \App\Models\Currency::where('is_active', true)->get();
        
        return view('agency.invoices.create', compact('serviceRequest', 'customers', 'services', 'currencies'));
    }
    
    /**
     * تخزين فاتورة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'request_id' => 'nullable|exists:service_requests,id',
            'currency_code' => 'required|exists:currencies,code',
            'due_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
        ]);
        
        try {
            DB::beginTransaction();
            
            // حساب المجاميع
            $subtotal = 0;
            $taxTotal = 0;
            $discountTotal = 0;
            
            foreach ($request->items as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $itemDiscount = $item['discount'] ?? 0;
                $itemTax = $item['tax'] ?? 0;
                
                $subtotal += $itemSubtotal;
                $discountTotal += $itemDiscount;
                $taxTotal += $itemTax;
            }
            
            $total = $subtotal - $discountTotal + $taxTotal;
            
            // إنشاء رقم الفاتورة
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            
            // إنشاء الفاتورة
            $invoice = Invoice::create([
                'user_id' => $request->user_id,
                'agency_id' => auth()->user()->agency_id,
                'request_id' => $request->request_id,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'tax' => $taxTotal,
                'discount' => $discountTotal,
                'total' => $total,
                'currency_code' => $request->currency_code,
                'status' => 'draft',
                'notes' => $request->notes,
                'due_date' => $request->due_date,
            ]);
            
            // إضافة بنود الفاتورة
            foreach ($request->items as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'tax' => $item['tax'] ?? 0,
                    'subtotal' => $itemSubtotal,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('agency.invoices.show', $invoice)
                           ->with('success', __('messages.invoice_created'));
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                        ->with('error', __('messages.invoice_create_failed'));
        }
    }
    
    /**
     * عرض تفاصيل فاتورة معينة
     */
    public function show(Invoice $invoice)
    {
        // التحقق من أن الفاتورة تنتمي للوكالة
        if ($invoice->agency_id !== auth()->user()->agency_id) {
            abort(403, __('messages.access_denied'));
        }
        
        $invoice->load(['user', 'request', 'items', 'payments']);
        
        return view('agency.invoices.show', compact('invoice'));
    }
}
