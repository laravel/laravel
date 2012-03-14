<?php namespace App\Commands;

class AssignRolesToAccount {

	public function __construct($uuid, $attributes)
	{
		$this->attributes = $attributes;
		$this->attributes['uuid'] = $uuid;
	}

}