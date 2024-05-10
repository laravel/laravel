<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $primaryKey = 'booking_id';

    protected $fillable = ['booking_id','guest_name','guest_email','check_in_date','check_out_date','room_type','num_guests','total_price','booking_status','customer_id'];

    // function customer(){
    //     return $this->belongsTo(Customer::class);
    // }

    function room(){
        return $this->belongsTo(Room::class);
    }
}
