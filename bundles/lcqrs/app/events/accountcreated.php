<?php namespace Jackzz\Events;

class AccountCreated {

	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

}