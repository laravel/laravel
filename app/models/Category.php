<?php

class Category extends Eloquent {

	protected $table = 'categories';

	protected $fillable = array( 'name', 'parent_id' );

    public $timestamps = false;

	public function parent() {
		return $this->belongsTo( 'Category', 'parent_id' );
	}

	public function children() {
		return $this->hasMany( 'Category', 'parent_id' );
	}

} 