<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class registro extends Model
{
    use HasFactory;
    protected $table = 'registro';
    protected $primaryKey = 'idusuario';
    protected $keyType = 'int';
    public $timestamps = false;
}
