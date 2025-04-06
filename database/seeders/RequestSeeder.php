<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Request as ServiceRequest;
use App\Models\User;
use App\Models\Service;
use App\Models\Agency;
use Carbon\Carbon;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // جلب وكالة اليمن
        $yemenAgency = Agency::where('email', 'info@yemen-travel.com')->first();
        
        if (!$yemenAgency) {
            return;
        }
        
        // جلب العملاء
        $customers = User::where('agency_id', $yemenAgency->id)
            ->where('user_type', 'customer')
            ->get();
            
        if ($customers->isEmpty()) {
            return;
        }
        
        // جلب الخدمات
        $services = Service::where('agency_id', $yemenAgency->id)->get();
        
        if ($services->isEmpty()) {
            return;
        }
        
        // إنشاء طلبات متنوعة
        foreach ($customers as $index => $customer) {
            // طلب موافقة أمنية
            $service = $services->where('type', 'security_approval')->first();
            if ($service) {
                ServiceRequest::create([
                    'service_id' => $service->id,
                    'customer_id' => $customer->id,
                    'agency_id' => $yemenAgency->id,
                    'details' => 'أحتاج إلى موافقة أمنية لزيارة مصر لمدة شهر بغرض السياحة.',
                    'priority' => 'normal',
                    'status' => 'pending',
                    'requested_date' => Carbon::now()->addDays(7),
                    'created_at' => Carbon::now()->subDays(5),
                    'updated_at' => Carbon::now()->subDays(5),
                ]);
            }
            
            // طلب نقل بري
            $service = $services->where('type', 'transportation')->first();
            if ($service) {
                ServiceRequest::create([
                    'service_id' => $service->id,
                    'customer_id' => $customer->id,
                    'agency_id' => $yemenAgency->id,
                    'details' => 'أحتاج إلى خدمة نقل من صنعاء إلى عدن لشخصين بتاريخ ' . Carbon::now()->addDays(10)->format('Y-m-d'),
                    'priority' => 'urgent',
                    'status' => 'in_progress',
                    'requested_date' => Carbon::now()->addDays(10),
                    'created_at' => Carbon::now()->subDays(3),
                    'updated_at' => Carbon::now()->subDays(2),
                ]);
            }
            
            // طلب تذكرة طيران
            $service = $services->where('type', 'flight')->first();
            if ($service) {
                ServiceRequest::create([
                    'service_id' => $service->id,
                    'customer_id' => $customer->id,
                    'agency_id' => $yemenAgency->id,
                    'details' => 'أحتاج حجز تذكرة طيران من صنعاء إلى القاهرة ذهاب وعودة للفترة من ' . 
                             Carbon::now()->addDays(15)->format('Y-m-d') . ' إلى ' . 
                             Carbon::now()->addDays(30)->format('Y-m-d'),
                    'priority' => 'normal',
                    'status' => 'completed',
                    'requested_date' => Carbon::now()->addDays(15),
                    'created_at' => Carbon::now()->subDays(15),
                    'updated_at' => Carbon::now()->subDays(10),
                ]);
            }
        }
    }
}
