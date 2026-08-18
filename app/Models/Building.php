<?php
namespace App\Models;
class Building extends TenantModel { protected $casts=['is_active'=>'boolean']; public function rooms(){return $this->hasMany(Room::class);} }
