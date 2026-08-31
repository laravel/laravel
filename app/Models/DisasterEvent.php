<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisasterEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'severity',
        'node_id',
        'location',
        'status',
        'started_at',
        'resolved_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function node()
    {
        return $this->belongsTo(Node::class);
    }

    public function systemState()
    {
        return $this->hasOne(SystemState::class, 'current_disaster_id');
    }
}
