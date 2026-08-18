<?php
namespace App\Models; class DeliveryChangeRequest extends TenantModel { protected $casts=['reviewed_at'=>'datetime']; public function delivery(){return $this->belongsTo(Delivery::class);} public function requester(){return $this->belongsTo(User::class,'requested_by');} public function reviewer(){return $this->belongsTo(User::class,'reviewed_by');} }
