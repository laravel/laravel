<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'reference',
        'payment_method',
        'payment_url',
        'pay_code',
        'amount',
        'status',
        'paid_at',
        'expired_at',
    ];

    protected $casts = [
        'amount'     => 'integer',
        'paid_at'    => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
