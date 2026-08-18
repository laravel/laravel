<?php
namespace App\Models;
class MealPause extends TenantModel { protected $casts=['starts_on'=>'date','resumes_on'=>'date']; public function customer(){return $this->belongsTo(Customer::class);} }
