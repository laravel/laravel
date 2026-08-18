<?php
namespace App\Models;
class InvoiceLine extends TenantModel { protected $casts=['metadata'=>'array','quantity'=>'decimal:2','unit_price'=>'decimal:2','amount'=>'decimal:2']; public function invoice(){return $this->belongsTo(Invoice::class);} }
