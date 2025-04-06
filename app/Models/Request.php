<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id', 'customer_id', 'agency_id', 'details', 'priority', 'status', 'requested_date'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
