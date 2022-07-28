<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artwork extends Model
{
    use HasFactory;
    protected $fillable = ['description', 'artworks_order_id', 'requiredqty', 'jobrun', 'labelrepeat', 'printedqty', 'artworks_media_id',];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'artworks_order_id');
    }
}


// $table->string('');
// $table->bigInteger('artworks_order_id');
// $table->integer('requiredqty');
// $table->integer('jobrun');
// $table->integer('labelrepeat');
// $table->integer('printedqty');
// $table->bigInteger('artworks_media_id');