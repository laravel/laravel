<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'department_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
