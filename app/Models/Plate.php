<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plate extends Model
{
    use HasFactory;
    protected $fillable =   [ 'plateno', 'plates_media_id' ];

    
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'plates_media_id');
    }
}
