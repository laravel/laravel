<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Company extends Model { use HasUuids; protected $guarded=[]; protected $casts=['is_active'=>'boolean','delivery_cutoff_time'=>'datetime:H:i','settings'=>'array','low_balance_threshold'=>'decimal:2']; public function uniqueIds():array{return ['uuid'];} public function getRouteKeyName():string{return 'uuid';} public function users(){return $this->hasMany(User::class);} }
