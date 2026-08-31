<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'rfid_tag',
        'action',
        'location',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
