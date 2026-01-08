<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metadatas extends Model
{
    protected $connection = 'mysql';

    protected $table = "metadatas";

    protected $fillable = ['name', 'comment', 'value', 'metadatatype', 'is_visible'];

    public $timestamps = false;

    public function metadatatypes()
    {
        return $this->belongsTo('App\Models\MetadataTypes', 'metadatatype');
    }
}
