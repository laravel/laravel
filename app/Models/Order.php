<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;




use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['orderno', 'orders_customer_id', 'status'];


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'orders_customer_id');
    }
}