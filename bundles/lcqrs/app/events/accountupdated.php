<?php namespace Jackzz\Events;

class AccountUpdated {

	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

}