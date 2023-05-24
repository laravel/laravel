<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'rolename',
        'permission_id'
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function permission()
    {
        return $this->hasMany(Permission::class);
    }
}
