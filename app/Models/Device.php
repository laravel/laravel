<?php
namespace App\Models;
class Device extends TenantModel { protected $casts=['approved_at'=>'datetime','disabled_at'=>'datetime','last_seen_at'=>'datetime']; public function user(){return $this->belongsTo(User::class);} }
