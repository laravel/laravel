<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location_x',
        'location_y',
        'zone_id',
        'status',
        'last_heartbeat',
    ];

    protected $casts = [
        'location_x' => 'decimal:6',
        'location_y' => 'decimal:6',
        'last_heartbeat' => 'datetime',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function sensorReadings()
    {
        return $this->hasMany(SensorReading::class);
    }

    public function disasterEvents()
    {
        return $this->hasMany(DisasterEvent::class);
    }

    public function occupancyLogs()
    {
        return $this->hasMany(OccupancyLog::class);
    }
}
