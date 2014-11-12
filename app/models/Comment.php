<?php

class Comment  extends Eloquent {
    
    protected $fillable = array('title', 'post_id');

    public function post()
    {
        return $this->belongsTo('Post');
    }

}
