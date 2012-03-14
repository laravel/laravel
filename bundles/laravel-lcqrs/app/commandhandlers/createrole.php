<?php namespace App\CommandHandlers;

use App\Entities\Role;

class CreateRole {
	
	public function __construct($command)
	{
		$role = new Role;
		$role->create($command->attributes);
	}

}