<?php

class Post extends Eloquent {
    
    protected $fillable = array('title', 'user_id');
    
    public function user()
    {
        return $this->belongsTo('User');
    }

    public function comments()
    {
        return $this->hasMany('Comment');
    }

}