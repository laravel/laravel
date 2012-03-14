<?php namespace App\Events;

class RolesAssignedToAccount {
	
	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

}