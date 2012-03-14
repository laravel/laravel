<?php namespace App\Commands;

class UnassignRolesFromAccount {

	public function __construct($uuid, $attributes)
	{
		$this->attributes = $attributes;
		$this->attributes['uuid'] = $uuid;
	}

}