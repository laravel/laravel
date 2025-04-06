<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Agency;
use App\Models\User;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // جلب الوكالات المنشأة سابقاً
        $yemenAgency = Agency::where('email', 'info@yemen-travel.com')->first();
        $gulfAgency = Agency::where('email', 'info@gulf-travel.com')->first();
        
        if ($yemenAgency) {
            // خدمات الموافقات الأمنية
            $securityApproval1 = Service::create([
                'agency_id' => $yemenAgency->id,
                'name' => 'موافقة أمنية - مصر',
                'description' => 'خدمة استخراج الموافقة الأمنية لدخول مصر',
                'type' => 'security_approval',
                'status' => 'active',
                'base_price' => 300,
                'commission_rate' => 10,
            ]);
            
            $securityApproval2 = Service::create([
                'agency_id' => $yemenAgency->id,
                'name' => 'موافقة أمنية - الأردن',
                'description' => 'خدمة استخراج الموافقة الأمنية لدخول الأردن',
                'type' => 'security_approval',
                'status' => 'active',
                'base_price' => 250,
                'commission_rate' => 10,
            ]);
            
            // خدمات النقل البري
            $transportation1 = Service::create([
                'agency_id' => $yemenAgency->id,
                'name' => 'نقل بري VIP - صنعاء إلى عدن',
                'description' => 'خدمة نقل VIP من صنعاء إلى عدن',
                'type' => 'transportation',
                'status' => 'active',
                'base_price' => 500,
                'commission_rate' => 15,
            ]);
            
            $transportation2 = Service::create([
                'agency_id' => $yemenAgency->id,
                'name' => 'نقل بري عادي - صنعاء إلى عدن',
                'description' => 'خدمة نقل عادية من صنعاء إلى عدن',
                'type' => 'transportation',
                'status' => 'active',
                'base_price' => 300,
                'commission_rate' => 15,
            ]);
            
            // خدمات تذاكر الطيران
            Service::create([
                'agency_id' => $yemenAgency->id,
                'name' => 'حجز تذاكر طيران داخلية',
                'description' => 'خدمة حجز تذاكر الطيران الداخلية بين مختلف المدن اليمنية',
                'type' => 'flight',
                'status' => 'active',
                'base_price' => 1000,
                'commission_rate' => 5,
            ]);
            
            Service::create([
                'agency_id' => $yemenAgency->id,
                'name' => 'حجز تذاكر طيران دولية',
                'description' => 'خدمة حجز تذاكر الطيران الدولية إلى مختلف دول العالم',
                'type' => 'flight',
                'status' => 'active',
                'base_price' => 2500,
                'commission_rate' => 7,
            ]);
            
            // خدمات الحج والعمرة
            Service::create([
                'agency_id' => $yemenAgency->id,
                'name' => 'برنامج عمرة - اقتصادي',
                'description' => 'برنامج عمرة اقتصادي لمدة 10 أيام شامل السكن والنقل',
                'type' => 'hajj_umrah',
                'status' => 'active',
                'base_price' => 5000,
                'commission_rate' => 8,
            ]);
            
            Service::create([
                'agency_id' => $yemenAgency->id,
                'name' => 'برنامج حج - VIP',
                'description' => 'برنامج حج متميز مع سكن قريب من الحرم ومواصلات خاصة',
                'type' => 'hajj_umrah',
                'status' => 'active',
                'base_price' => 15000,
                'commission_rate' => 10,
            ]);
            
            // خدمات إصدار الجوازات
            Service::create([
                'agency_id' => $yemenAgency->id,
                'name' => 'إصدار جواز سفر - صنعاء',
                'description' => 'خدمة إصدار جواز سفر جديد من صنعاء',
                'type' => 'passport',
                'status' => 'active',
                'base_price' => 800,
                'commission_rate' => 12,
            ]);
            
            // ربط السبوكلاء بالخدمات
            $subagents = User::where('agency_id', $yemenAgency->id)
                ->where('user_type', 'subagent')
                ->get();
                
            foreach ($subagents as $subagent) {
                // ربط خدمات مختلفة مع السبوكلاء
                $securityApproval1->subagents()->attach($subagent->id, [
                    'is_active' => true,
                    'custom_commission_rate' => 10,
                ]);
                
                $securityApproval2->subagents()->attach($subagent->id, [
                    'is_active' => true,
                    'custom_commission_rate' => 10,
                ]);
                
                $transportation1->subagents()->attach($subagent->id, [
                    'is_active' => true,
                    'custom_commission_rate' => 15,
                ]);
            }
        }
        
        // خدمات لوكالة الخليج
        if ($gulfAgency) {
            // إضافة بعض الخدمات لوكالة الخليج
            $gulfService1 = Service::create([
                'agency_id' => $gulfAgency->id,
                'name' => 'نقل بري VIP - عدن إلى المكلا',
                'description' => 'خدمة نقل VIP من عدن إلى المكلا',
                'type' => 'transportation',
                'status' => 'active',
                'base_price' => 450,
                'commission_rate' => 12,
            ]);
            
            Service::create([
                'agency_id' => $gulfAgency->id,
                'name' => 'إصدار جواز سفر - عدن',
                'description' => 'خدمة إصدار جواز سفر جديد من عدن',
                'type' => 'passport',
                'status' => 'active',
                'base_price' => 750,
                'commission_rate' => 10,
            ]);
            
            // ربط السبوكلاء بالخدمات
            $gulfSubagents = User::where('agency_id', $gulfAgency->id)
                ->where('user_type', 'subagent')
                ->get();
                
            foreach ($gulfSubagents as $subagent) {
                $gulfService1->subagents()->attach($subagent->id, [
                    'is_active' => true,
                    'custom_commission_rate' => 12,
                ]);
            }
        }
    }
}
