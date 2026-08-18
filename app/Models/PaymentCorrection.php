<?php
namespace App\Models; class PaymentCorrection extends TenantModel { protected $casts=['corrected_amount'=>'decimal:2','reviewed_at'=>'datetime']; public function payment(){return $this->belongsTo(Payment::class);} public function requester(){return $this->belongsTo(User::class,'requested_by');} }
