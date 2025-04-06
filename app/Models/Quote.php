<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id', 'subagent_id', 'price', 'commission_amount', 'details', 'status'
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function subagent()
    {
        return $this->belongsTo(User::class, 'subagent_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
