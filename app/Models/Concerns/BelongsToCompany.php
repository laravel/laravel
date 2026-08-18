<?php
namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::creating(function ($model): void {
            if (! $model->company_id && auth()->check()) $model->company_id = auth()->user()->company_id;
        });
        static::addGlobalScope('company', function (Builder $query): void {
            $user = auth()->user();
            if ($user instanceof User && ! $user->isPlatformAdmin()) $query->where($query->qualifyColumn('company_id'), $user->company_id);
        });
    }
}
