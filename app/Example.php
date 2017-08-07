<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];
    
    public function setDescriptionAttribute(string $description): void
    {
        $this->set('description', strip_tags($description));
    }
}
