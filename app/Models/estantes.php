<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class estantes extends Model
{
    use HasFactory;

    protected $table = 'estantes';
    protected $primaryKey = 'idestante';
    protected $keyType = 'int';
    public $timestamps = false;
}
