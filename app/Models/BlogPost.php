<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','excerpt','content','seo_title','seo_description','status'
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
