<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'node_id',
        'sensor_type',
        'value',
        'unit',
        'timestamp',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'timestamp' => 'datetime',
    ];

    public function node()
    {
        return $this->belongsTo(Node::class);
    }
}
