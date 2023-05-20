<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class libros extends Model
{
    use HasFactory;

    protected $table = 'libros';
    protected $primaryKey = 'idlibro';
    protected $keyType = 'int';
    public $timestamps = false;
}
