<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'request_id',
        'invoice_id',
        'payment_method',
        'payment_gateway',
        'transaction_id',
        'amount',
        'currency_code',
        'status',
        'gateway_response',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gateway_response' => 'array',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم الذي قام بالدفع
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع الطلب المرتبط بالدفع
     */
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * العلاقة مع الفاتورة المرتبطة بالدفع
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
