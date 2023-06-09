<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class session extends Model
{
    use HasFactory;

    protected $table = 'session';
    protected $primaryKey = 'token';
    protected $KeyType = 'string';
    public $timestamps = false;
}
