<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
        'file_name',
    ];

    // Relasi ke User (guru)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
