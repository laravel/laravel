<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'title',
        'description',
        'duration',
        'total_questions',
        'status',
        'passing_marks'
    ];
    
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
