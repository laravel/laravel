<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetadataTypes extends Model
{
    protected $table = "metadata_types";

    protected $fillable = ['name', 'comment'];

    public $timestamps = false;
}
