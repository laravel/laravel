<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingFile extends Model
{
    protected $fillable = [
        'filename', 'path', 'region_id', 'partner_id', 'detected_at'
    ];
}

