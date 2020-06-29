<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Job extends Model
{
    protected $table = 'jobs';
    protected $fillable = [
        'available_at',
    ];

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeJobsOfToday(Builder $query)
    {
        return $query->whereRaw('Date(available_at) = CURDATE()');
    }
}
