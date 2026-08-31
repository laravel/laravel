<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'rfid_tag',
        'guardian_name',
        'guardian_phone',
    ];

    public function occupancyLogs()
    {
        return $this->hasMany(OccupancyLog::class);
    }

    public function rfidLogs()
    {
        return $this->hasMany(RfidLog::class);
    }
}
