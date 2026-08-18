<?php
namespace App\Models;
class Room extends TenantModel { protected $casts=['delivery_enabled'=>'boolean']; public function building(){return $this->belongsTo(Building::class);} public function customers(){return $this->hasMany(Customer::class);} }
