<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'subagent_id',
        'price',
        'commission_amount',
        'details',
        'status',
        'currency_code',
    ];

    /**
     * Get the request that owns the quote.
     */
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get the subagent that owns the quote.
     */
    public function subagent()
    {
        return $this->belongsTo(User::class, 'subagent_id');
    }

    /**
     * Get the attachments for the quote.
     */
    public function attachments()
    {
        return $this->hasMany(QuoteAttachment::class);
    }
}
