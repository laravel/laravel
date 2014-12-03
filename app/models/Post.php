<?php

class Post extends \Eloquent {
	protected $guarded = [];

  public static function boot()
  {
    parent::boot();
    static::saving(function($post){
      $post->comments_count = 6; // for test purpose
      return true;
    });
  }

}