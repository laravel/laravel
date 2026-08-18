<?php
namespace App\Models; class NotificationLog extends TenantModel { protected $casts=['sent_at'=>'datetime','read_at'=>'datetime','metadata'=>'array']; public function customer(){return $this->belongsTo(Customer::class);} }
