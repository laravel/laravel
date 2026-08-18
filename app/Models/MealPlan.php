<?php
namespace App\Models;
use App\Enums\BillingType;
class MealPlan extends TenantModel { protected $casts=['billing_type'=>BillingType::class,'daily_rate'=>'decimal:2','monthly_rate'=>'decimal:2','credit_pauses'=>'boolean','credit_skips'=>'boolean','is_active'=>'boolean']; public function customerPlans(){return $this->hasMany(CustomerPlan::class);} }
