<?php
namespace App\Models;
use App\Enums\InvoiceStatus;
class Invoice extends TenantModel { protected $casts=['period_start'=>'date','period_end'=>'date','due_date'=>'date','issued_at'=>'datetime','status'=>InvoiceStatus::class,'previous_balance'=>'decimal:2','current_charges'=>'decimal:2','total_payable'=>'decimal:2','amount_paid'=>'decimal:2','balance'=>'decimal:2']; public function customer(){return $this->belongsTo(Customer::class);} public function lines(){return $this->hasMany(InvoiceLine::class);} public function payments(){return $this->hasMany(Payment::class);} }
