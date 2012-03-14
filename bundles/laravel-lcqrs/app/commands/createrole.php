<?php namespace App\Commands;

class CreateRole {

	public function __construct($uuid, $attributes)
	{
		$this->attributes = $attributes;
		$this->attributes['uuid'] = $uuid;
	}

}