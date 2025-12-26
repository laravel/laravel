<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestEntry extends Model
{
    protected $fillable = ['name', 'message'];
}
