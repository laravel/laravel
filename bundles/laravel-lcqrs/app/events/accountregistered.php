<?php namespace App\Events;

class AccountRegistered {

	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

}