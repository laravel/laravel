<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'solution',
    ];

    // Relasi ke User (pembuat laporan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
