<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $fillable = ['ID', 'name'];
    public $timestamps = false;

    public function users(){
        return $this->hasOne('App\Models\User',);
    }
}
