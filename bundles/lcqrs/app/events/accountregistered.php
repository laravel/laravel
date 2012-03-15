<?php namespace Jackzz\Events;

class AccountRegistered {

	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

}