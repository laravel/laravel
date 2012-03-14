<?php namespace App\Events;

class RoleCreated {

	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

}