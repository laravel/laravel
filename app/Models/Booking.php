<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'service',
        'details',
        'status',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
}
