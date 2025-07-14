<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    protected $fillable = [
        'attempt_id',
        'total_questions',
        'answers',
        'percentage',
        'score',
        'status',
        'duration',
    ];
}
