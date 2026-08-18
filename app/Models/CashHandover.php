<?php
namespace App\Models;
use App\Enums\HandoverStatus;
class CashHandover extends TenantModel { protected $casts=['status'=>HandoverStatus::class,'submitted_at'=>'datetime','verified_at'=>'datetime','amount_submitted'=>'decimal:2','verified_amount'=>'decimal:2','variance'=>'decimal:2']; public function employee(){return $this->belongsTo(User::class,'employee_id');} }
