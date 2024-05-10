<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = ['full_name','image_src','department_id','salary_type','salary_amt','Bio'];

    function department(){
        return $this->belongsTo(Department::class);
    }
}
