<?php
namespace App\Services;
use App\Models\AuditLog; use Illuminate\Database\Eloquent\Model;
class AuditService { public static function record(string $action, ?Model $model=null, array $old=[], array $new=[]): void { AuditLog::withoutGlobalScopes()->create(['company_id'=>$model?->company_id ?? auth()->user()?->company_id,'user_id'=>auth()->id(),'action'=>$action,'auditable_type'=>$model?->getMorphClass(),'auditable_id'=>$model?->getKey(),'old_values'=>$old ?: null,'new_values'=>$new ?: null,'ip_address'=>request()?->ip(),'user_agent'=>request()?->userAgent()]); } }
