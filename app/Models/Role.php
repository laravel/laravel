<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Un rol puede tener muchos usuarios
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}