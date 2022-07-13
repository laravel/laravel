<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Rotarydie extends Model
{
    use HasFactory;
    protected $table = 'rotarydies';

    protected $fillable  = [
        'customermark',
        'aroundsize', 'acrosssize',
        'aroundrepeat', 'acrossrepeat',
        'aroundgap', 'acrossgap',
        'cornerradius', 'media', 'rotarydies_cylinder_id'
    ];

    public function cylinder(): BelongsTo
    {
        return $this->belongsTo(Cylinder::class, 'rotarydies_cylinder_id');
    }
}