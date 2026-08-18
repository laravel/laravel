<?php
namespace App\Models;
class CustomerPlan extends TenantModel { protected $casts=['starts_on'=>'date','ends_on'=>'date','rate_override'=>'decimal:2']; public function customer(){return $this->belongsTo(Customer::class);} public function mealPlan(){return $this->belongsTo(MealPlan::class);} }
