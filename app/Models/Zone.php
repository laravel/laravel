<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'boundary_data',
    ];

    protected $casts = [
        'boundary_data' => 'array',
    ];

    public function nodes()
    {
        return $this->hasMany(Node::class);
    }

    public function occupancyLogs()
    {
        return $this->hasMany(OccupancyLog::class);
    }
}
