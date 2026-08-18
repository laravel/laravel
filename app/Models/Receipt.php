<?php
namespace App\Models;
class Receipt extends TenantModel { protected $casts=['shared_at'=>'datetime']; public function payment(){return $this->belongsTo(Payment::class);} }
