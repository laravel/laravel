<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users_table';
    protected $fillable = ['name', 'email', 'password',];

    // hidden
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
