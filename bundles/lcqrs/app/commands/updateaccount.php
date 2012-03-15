<?php namespace Jackzz\Commands;

class UpdateAccount {

	public function __construct($uuid, $attributes)
	{
		$this->attributes = $attributes;
		$this->attributes['uuid'] = $uuid;
	}

}