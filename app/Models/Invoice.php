<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
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
        'invoice_number',
        'subtotal',
        'tax',
        'discount',
        'total',
        'currency_code',
        'status',
        'notes',
        'due_date',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع الطلب
     */
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * العلاقة مع بنود الفاتورة
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * العلاقة مع المدفوعات
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * حساب المبلغ المدفوع من الفاتورة
     */
    public function getPaidAmountAttribute()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    /**
     * حساب المبلغ المتبقي من الفاتورة
     */
    public function getRemainingAmountAttribute()
    {
        return $this->total - $this->paid_amount;
    }

    /**
     * التحقق مما إذا كانت الفاتورة مدفوعة بالكامل
     */
    public function getIsPaidAttribute()
    {
        return $this->remaining_amount <= 0;
    }
}
