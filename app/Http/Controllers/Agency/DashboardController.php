<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use App\Models\Request as ServiceRequest;
use App\Models\Quote;
use App\Models\Transaction;

class DashboardController extends Controller
{
    /**
     * عرض لوحة تحكم الوكيل
     */
    public function index()
    {
        $agencyId = auth()->user()->agency_id;
        
        // إحصائيات
        $subagentsCount = User::where('agency_id', $agencyId)
            ->where('user_type', 'subagent')
            ->count();
            
        $customersCount = User::where('agency_id', $agencyId)
            ->where('user_type', 'customer')
            ->count();
            
        $requestsCount = ServiceRequest::where('agency_id', $agencyId)->count();
        
        $quotesCount = Quote::whereHas('request', function($query) use ($agencyId) {
            $query->where('agency_id', $agencyId);
        })->count();
        
        // آخر الطلبات
        $latestRequests = ServiceRequest::where('agency_id', $agencyId)
            ->latest()
            ->take(5)
            ->get();
        
        return view('agency.dashboard', compact(
            'subagentsCount',
            'customersCount',
            'requestsCount',
            'quotesCount',
            'latestRequests'
        ));
    }

    /**
     * تحديث معلومات الوكالة
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
                Storage::disk('public')->delete($agency->logo);
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
