<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\Request as ServiceRequest;
use App\Models\Service;
use App\Models\User;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    /**
     * عرض صفحة التقارير
     */
    public function index()
    {
        return view('agency.reports.index');
    }
    
    /**
     * تقرير الإيرادات حسب الخدمة
     */
    public function revenueByService(Request $request)
    {
        $agency_id = auth()->user()->agency_id;
        $dateRange = $this->getDateRange($request);
        
        $servicesRevenue = Quote::whereHas('request', function($query) use ($agency_id) {
                $query->where('agency_id', $agency_id);
            })
            ->where('status', 'customer_approved')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->select('requests.service_id', DB::raw('SUM(quotes.price) as total_revenue'), DB::raw('SUM(quotes.commission_amount) as total_commission'), DB::raw('COUNT(quotes.id) as quotes_count'))
            ->join('requests', 'quotes.request_id', '=', 'requests.id')
            ->groupBy('requests.service_id')
            ->with('request.service')
            ->get()
            ->map(function($item) {
                return [
                    'service_name' => $item->request->service->name,
                    'total_revenue' => $item->total_revenue,
                    'total_commission' => $item->total_commission,
                    'quotes_count' => $item->quotes_count,
                ];
            });
        
        return view('agency.reports.revenue-by-service', [
            'servicesRevenue' => $servicesRevenue,
            'dateRange' => $dateRange,
            'defaultCurrency' => Currency::where('is_default', true)->first()->code,
        ]);
    }
    
    /**
     * تقرير الإيرادات حسب السبوكيل
     */
    public function revenueBySubagent(Request $request)
    {
        $agency_id = auth()->user()->agency_id;
        $dateRange = $this->getDateRange($request);
        
        $subagentsRevenue = Quote::whereHas('request', function($query) use ($agency_id) {
                $query->where('agency_id', $agency_id);
            })
            ->where('status', 'customer_approved')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->select('subagent_id', DB::raw('SUM(price) as total_revenue'), DB::raw('SUM(commission_amount) as total_commission'), DB::raw('COUNT(id) as quotes_count'))
            ->groupBy('subagent_id')
            ->with('subagent')
            ->get()
            ->map(function($item) {
                return [
                    'subagent_name' => $item->subagent->name,
                    'total_revenue' => $item->total_revenue,
                    'total_commission' => $item->total_commission,
                    'quotes_count' => $item->quotes_count,
                    'net_amount' => $item->total_revenue - $item->total_commission,
                ];
            });
        
        return view('agency.reports.revenue-by-subagent', [
            'subagentsRevenue' => $subagentsRevenue,
            'dateRange' => $dateRange,
            'defaultCurrency' => Currency::where('is_default', true)->first()->code,
        ]);
    }
    
    /**
     * تصدير التقرير كملف إكسل
     */
    public function export(Request $request)
    {
        // This is a placeholder for Excel export functionality
        // In a real implementation, you would generate an Excel file here
        // using a package like Laravel Excel (maatwebsite/excel)
        
        return back()->with('info', 'سيتم تنفيذ هذه الميزة في الإصدار القادم.');
    }
    
    /**
     * الحصول على نطاق التاريخ المطلوب
     */
    private function getDateRange(Request $request)
    {
        $period = $request->input('period', 'month');
        $start = null;
        $end = Carbon::now();
        
        switch($period) {
            case 'week':
                $start = Carbon::now()->subWeek();
                break;
            case 'month':
                $start = Carbon::now()->startOfMonth();
                break;
            case 'quarter':
                $start = Carbon::now()->startOfQuarter();
                break;
            case 'year':
                $start = Carbon::now()->startOfYear();
                break;
            case 'custom':
                $start = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonth();
                $end = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now();
                break;
            default:
                $start = Carbon::now()->subMonth();
        }
        
        return [
            'start' => $start,
            'end' => $end,
            'period' => $period
        ];
    }
}
