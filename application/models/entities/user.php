<?php

namespace Entities;

class User extends \Eloquent {
	
	public function friends()
	{
		return $this->has_many('Entities\\Friend');
	}

	public function roles()
	{
		return $this->has_and_belongs_to_many('Role');
	}

}

class Friend extends \Eloquent {}