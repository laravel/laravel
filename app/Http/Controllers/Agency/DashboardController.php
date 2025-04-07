<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use App\Models\Request as ServiceRequest;
use App\Models\Quote;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the agency dashboard.
     */
    public function index()
    {
        $agency_id = auth()->user()->agency_id;
        
        // إحصائيات عامة
        $stats = [
            'services_count' => Service::where('agency_id', $agency_id)->count(),
            'active_services_count' => Service::where('agency_id', $agency_id)->where('status', 'active')->count(),
            'subagents_count' => User::where('agency_id', $agency_id)->where('user_type', 'subagent')->count(),
            'customers_count' => User::where('agency_id', $agency_id)->where('user_type', 'customer')->count(),
            'requests_count' => ServiceRequest::where('agency_id', $agency_id)->count(),
            'quotes_count' => Quote::whereHas('request', function($query) use ($agency_id) {
                $query->where('agency_id', $agency_id);
            })->count(),
            'pending_requests_count' => ServiceRequest::where('agency_id', $agency_id)->where('status', 'pending')->count(),
            'in_progress_requests_count' => ServiceRequest::where('agency_id', $agency_id)->where('status', 'in_progress')->count(),
            'completed_requests_count' => ServiceRequest::where('agency_id', $agency_id)->where('status', 'completed')->count(),
        ];
        
        // الخدمات الأكثر طلباً
        $topServices = ServiceRequest::where('agency_id', $agency_id)
            ->select('service_id', DB::raw('count(*) as count'))
            ->with('service')
            ->groupBy('service_id')
            ->orderByDesc('count')
            ->take(5)
            ->get();
        
        // السبوكلاء الأكثر نشاطاً
        $topSubagents = Quote::whereHas('request', function($query) use ($agency_id) {
                $query->where('agency_id', $agency_id);
            })
            ->select('subagent_id', DB::raw('count(*) as count'))
            ->with('subagent')
            ->groupBy('subagent_id')
            ->orderByDesc('count')
            ->take(5)
            ->get();
        
        // إحصائيات العملاء
        $topCustomers = ServiceRequest::where('agency_id', $agency_id)
            ->select('customer_id', DB::raw('count(*) as count'))
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('count')
            ->take(5)
            ->get();
        
        // إحصائيات الطلبات حسب الأشهر - طريقة متوافقة مع SQLite
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $requestsByMonth = ServiceRequest::where('agency_id', $agency_id)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y-m');
            })
            ->map(function ($items, $yearMonth) {
                // استخراج السنة والشهر من التنسيق 'YYYY-MM'
                [$year, $month] = explode('-', $yearMonth);
                $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F');
                
                return [
                    'month' => $monthName,
                    'count' => count($items),
                ];
            })
            ->sortBy(function ($item, $key) {
                return $key; // ترتيب حسب السنة-الشهر
            })
            ->values()
            ->toArray(); // تحويل إلى مصفوفة مفهرسة
        
        // آخر الطلبات
        $latestRequests = ServiceRequest::where('agency_id', $agency_id)
            ->with(['customer', 'service'])
            ->latest()
            ->take(5)
            ->get();
        
        // آخر عروض الأسعار
        $latestQuotes = Quote::whereHas('request', function($query) use ($agency_id) {
                $query->where('agency_id', $agency_id);
            })
            ->with(['subagent', 'request', 'request.service'])
            ->latest()
            ->take(5)
            ->get();

        // إحصائيات العملات
        $quotesByCurrency = Quote::whereHas('request', function($query) use ($agency_id) {
                $query->where('agency_id', $agency_id);
            })
            ->select('currency_code', DB::raw('count(*) as count'), DB::raw('sum(price) as total'))
            ->groupBy('currency_code')
            ->get();
        
        return view('agency.dashboard', compact(
            'stats',
            'topServices',
            'topSubagents',
            'topCustomers',
            'requestsByMonth',
            'latestRequests',
            'latestQuotes',
            'quotesByCurrency'
        ));
    }

    /**
     * تحديث معلومات الوكالة.
     */
    public function updateAgencyInfo(Request $request)
    {
        $request->validate([
            'agency_name' => 'required|string|max:255',
            'agency_email' => 'required|email',
            'agency_phone' => 'required|string|max:20',
            'agency_address' => 'nullable|string',
            'agency_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $agency = auth()->user()->agency;
        $agency->name = $request->agency_name;
        $agency->email = $request->agency_email;
        $agency->phone = $request->agency_phone;
        $agency->address = $request->agency_address;
        
        if ($request->hasFile('agency_logo')) {
            // حذف الشعار القديم إذا وجد
            if ($agency->logo) {
                \Storage::disk('public')->delete($agency->logo);
            }
            
            // تخزين الشعار الجديد
            $logo = $request->file('agency_logo');
            $logoPath = $logo->store('agency_logos', 'public');
            $agency->logo = $logoPath;
        }
        
        $agency->save();
        
        return redirect()->route('profile.edit')->with('success', 'تم تحديث معلومات الوكالة بنجاح');
    }
}
