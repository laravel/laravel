<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Article extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'title',
        'slug',
        'sub_title',
        'seo_keywords',
        'seo_description',
        'short_description',
        'full_description',
        'thumbnail',
        'status',
    ];

    public function scopeExceptColumns(Builder $query,array$exceptColumns)
    {
        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        return $query->select(array_diff($columns,$exceptColumns));
    }

}
