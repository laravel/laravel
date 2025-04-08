<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id', 'name', 'description', 'type', 'status', 'base_price', 'commission_rate'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function subagents()
    {
        return $this->belongsToMany(User::class, 'service_subagent')
                    ->withPivot('is_active', 'custom_commission_rate')
                    ->withTimestamps();
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
