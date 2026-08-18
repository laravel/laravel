<?php
namespace App\Models; class DailyClosing extends TenantModel { protected $casts=['closing_date'=>'date','closed_at'=>'datetime','cash_total'=>'decimal:2','card_total'=>'decimal:2','cash_submitted'=>'decimal:2','pending_cash'=>'decimal:2','variance'=>'decimal:2']; public function employee(){return $this->belongsTo(User::class,'employee_id');} }
