<?php
namespace App\Models;
use App\Enums\DeliveryStatus;
class Delivery extends TenantModel { protected $casts=['delivery_date'=>'date','status'=>DeliveryStatus::class,'confirmed_at'=>'datetime','requires_approval'=>'boolean']; public function customer(){return $this->belongsTo(Customer::class);} public function recorder(){return $this->belongsTo(User::class,'recorded_by');} }
