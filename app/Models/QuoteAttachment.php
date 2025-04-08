<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'name',
        'file_path',
        'file_type',
    ];

    /**
     * Get the quote that owns the attachment.
     */
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
