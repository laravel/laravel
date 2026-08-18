<?php
namespace App\Models;
use App\Enums\PaymentMode;
class Payment extends TenantModel { protected $casts=['paid_at'=>'datetime','mode'=>PaymentMode::class,'amount'=>'decimal:2']; public function customer(){return $this->belongsTo(Customer::class);} public function invoice(){return $this->belongsTo(Invoice::class);} public function employee(){return $this->belongsTo(User::class,'employee_id');} public function receipt(){return $this->hasOne(Receipt::class);} }
