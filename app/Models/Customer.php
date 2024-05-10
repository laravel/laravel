<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable=['full_name','email','password','mobile','address','photo','roomtypes'];
    

    function bookings(){
        return $this->hasMany(Booking::class);
    }
}
