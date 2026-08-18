<?php
namespace App\Models;
class LedgerEntry extends TenantModel { public const UPDATED_AT=null; protected $casts=['debit'=>'decimal:2','credit'=>'decimal:2','running_balance'=>'decimal:2']; public function customer(){return $this->belongsTo(Customer::class);} public function source(){return $this->morphTo();} }
