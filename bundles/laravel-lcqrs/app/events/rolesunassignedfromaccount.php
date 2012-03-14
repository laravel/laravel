<?php namespace App\Events;

class RolesUnassignedFromAccount {
	
	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

}