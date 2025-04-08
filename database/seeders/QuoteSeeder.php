<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quote;
use App\Models\Request as ServiceRequest;
use App\Models\User;
use Carbon\Carbon;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // جلب السبوكلاء
        $subagents = User::where('user_type', 'subagent')->get();
        
        if ($subagents->isEmpty()) {
            return;
        }
        
        // جلب الطلبات
        $requests = ServiceRequest::all();
        
        if ($requests->isEmpty()) {
            return;
        }
        
        foreach ($requests as $request) {
            // تجنب الطلبات المكتملة (نريد المعلقة أو قيد التنفيذ)
            if ($request->status === 'completed') {
                continue;
            }
            
            // جلب السبوكلاء المرتبطين بنفس الوكالة
            $agencySubagents = $subagents->where('agency_id', $request->agency_id);
            
            if ($agencySubagents->isEmpty()) {
                continue;
            }
            
            // تقديم عروض أسعار من بعض السبوكلاء
            foreach ($agencySubagents->take(rand(1, 2)) as $subagent) {
                // التأكد من أن الخدمة متاحة للسبوكيل
                $serviceIds = $subagent->services->pluck('id')->toArray();
                
                if (!in_array($request->service_id, $serviceIds)) {
                    continue;
                }
                
                // السعر الأساسي للخدمة
                $service = $request->service;
                $basePrice = $service->base_price;
                
                // حساب سعر العرض (+/- 10% من السعر الأساسي)
                $priceVariation = $basePrice * (rand(-10, 10) / 100);
                $quotePrice = $basePrice + $priceVariation;
                
                // حساب العمولة
                $commissionRate = $service->commission_rate;
                $commissionAmount = $quotePrice * ($commissionRate / 100);
                
                // إنشاء عرض السعر
                $status = rand(0, 4);
                $statusOptions = ['pending', 'agency_approved', 'agency_rejected', 'customer_approved', 'customer_rejected'];
                
                Quote::create([
                    'request_id' => $request->id,
                    'subagent_id' => $subagent->id,
                    'price' => round($quotePrice, 2),
                    'commission_amount' => round($commissionAmount, 2),
                    'details' => 'عرض سعر للخدمة المطلوبة. السعر شامل كافة الرسوم والضرائب.',
                    'status' => $statusOptions[$status],
                    'created_at' => Carbon::now()->subDays(rand(1, 3)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 1)),
                ]);
            }
        }
    }
}
