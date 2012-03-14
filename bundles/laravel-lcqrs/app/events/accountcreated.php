<?php namespace App\Events;

class AccountCreated {

	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

}