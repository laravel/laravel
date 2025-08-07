<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'model',
        'temperature',
        'prompt',
        'avatar_url',
        'welcome_message',
        'category_id',
        'config',
        'is_public',
    ];

    protected $casts = [
        'config' => 'array',
        'is_public' => 'boolean',
        'temperature' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
