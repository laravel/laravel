<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    // function roomtypeimgs(){
    //     return $this->hasMany(Roomtypeimage::class,'room_type_id');
    // }
    protected $fillable=['title','price','detail','img_src'];

}
