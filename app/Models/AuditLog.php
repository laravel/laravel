<?php
namespace App\Models;
use App\Models\Concerns\BelongsToCompany; use Illuminate\Database\Eloquent\Model;
class AuditLog extends Model { use BelongsToCompany; public const UPDATED_AT=null; protected $guarded=[]; protected $casts=['old_values'=>'array','new_values'=>'array']; public function auditable(){return $this->morphTo();} public function user(){return $this->belongsTo(User::class);} }
