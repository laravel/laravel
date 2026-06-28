<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'supplier_code',
        'supplier_codes',   // JSON: {"digiflazz": "ml-86", "vipreseller": "ML86D"}
        'base_price',
        'sell_price',
        'status',
    ];

    protected $casts = [
        'status'         => 'boolean',
        'base_price'     => 'integer',
        'sell_price'     => 'integer',
        'supplier_codes' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
