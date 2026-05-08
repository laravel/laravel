<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'stock',
        'capital', 'image', 'category', 'is_available',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'stock'        => 'decimal:2',
        'capital'      => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}