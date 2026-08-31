<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemState extends Model
{
    use HasFactory;

    protected $table = 'system_state';

    protected $fillable = [
        'disaster_mode',
        'check_occupancy',
        'current_disaster_id',
    ];

    protected $casts = [
        'disaster_mode' => 'boolean',
        'check_occupancy' => 'boolean',
    ];

    public function currentDisaster()
    {
        return $this->belongsTo(DisasterEvent::class, 'current_disaster_id');
    }
}
