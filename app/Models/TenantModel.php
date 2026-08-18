<?php
namespace App\Models;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
abstract class TenantModel extends Model { use BelongsToCompany, HasUuids; protected $guarded=[]; public function uniqueIds(): array { return ['uuid']; } public function getRouteKeyName(): string { return 'uuid'; } public function company(){return $this->belongsTo(Company::class);} }
