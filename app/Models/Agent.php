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
        'max_tokens',
        'top_p',
        'frequency_penalty',
        'presence_penalty',
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
        'top_p' => 'float',
        'frequency_penalty' => 'float',
        'presence_penalty' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tiers()
    {
        return $this->belongsToMany(Tier::class);
    }

    public function requiredMinCredits(): ?int
    {
        $min = $this->tiers()->min('min_credits');
        return $min ? (int) $min : null;
    }
}
