<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','min_credits','description'
    ];

    public function agents()
    {
        return $this->belongsToMany(Agent::class);
    }
}
