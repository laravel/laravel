<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * In fillable schrijf je welke velden je model moet hebben. Id en timestamp bestaan automatisch al, en hoef je niet op te schrijven.
     */
    protected $fillable = [
        'title',
        'body',
    ];
}