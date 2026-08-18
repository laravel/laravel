<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\{Building,Company,Customer,CustomerPlan,MealPlan,Room,User};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create(['name'=>'Platform Administrator','email'=>'platform@example.com','password'=>'ChangeMe!12345','role'=>Role::PlatformAdmin]);
        $company = Company::create(['name'=>'Demo Catering LLC','code'=>'DEMO']);
        User::factory()->create(['company_id'=>$company->id,'name'=>'Demo Company Admin','email'=>'admin@demo.test','password'=>'ChangeMe!12345','role'=>Role::CompanyAdmin]);
        User::factory()->create(['company_id'=>$company->id,'name'=>'Demo Collector','email'=>'collector@demo.test','password'=>'ChangeMe!12345','role'=>Role::CollectionEmployee]);
        $building = Building::withoutGlobalScopes()->create(['company_id'=>$company->id,'name'=>'Al Noor Residence','code'=>'ANR','address'=>'Dubai']);
        $room = Room::withoutGlobalScopes()->create(['company_id'=>$company->id,'building_id'=>$building->id,'number'=>'101','floor'=>'1','capacity'=>6]);
        $plan = MealPlan::withoutGlobalScopes()->create(['company_id'=>$company->id,'name'=>'Both Meals Monthly','meal_option'=>'both','billing_type'=>'fixed_monthly','monthly_rate'=>450]);
        $customer = Customer::withoutGlobalScopes()->create(['company_id'=>$company->id,'building_id'=>$building->id,'room_id'=>$room->id,'customer_code'=>'C-0001','name'=>'Demo Worker','passport_number'=>'P1234567','phone'=>'971500000000','start_date'=>today()]);
        CustomerPlan::withoutGlobalScopes()->create(['company_id'=>$company->id,'customer_id'=>$customer->id,'meal_plan_id'=>$plan->id,'starts_on'=>today()->startOfMonth()]);
    }
}
