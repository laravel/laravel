<?php namespace App\Events;

class AccountUpdated {

	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

}