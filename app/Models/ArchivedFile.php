<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedFile extends Model
{
    protected $fillable = [
        'filename', 'region_id', 'partner_id', 'moved_at'
    ];
}


