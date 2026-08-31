<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OccupancyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'node_id',
        'zone_id',
        'action',
        'method',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function node()
    {
        return $this->belongsTo(Node::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
