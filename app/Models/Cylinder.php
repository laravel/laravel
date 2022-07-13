<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Cylinder extends Model
{
    use HasFactory;
    protected $fillable = ['teeth', 'circumference_mm', 'circumference_inch', 'machine1', 'machine2'];
    protected $table = 'cylinders';

    protected $casts = [
        'machine1' => 'boolean',
        'machine2' => 'boolean',

    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Rotarydie::class);
    }
}