<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $filable = [ 
        'user_id',
        'nome',
        'email',
        'telefone',
        'empresa',
        'tel_comercial',
     ];

     public function user(){
        return $this->belongsTo(related: User::class);
     }
}
