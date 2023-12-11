<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    public $incrementing = false;

    protected $casts = [
        'updated_at' => 'datetime:d/m/Y h:i A',
        'created_at' => 'datetime:d/m/Y h:i A',
    ];
}
