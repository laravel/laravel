<?php namespace Jackzz\CommandHandlers;

use Jackzz\Entities\Role;

class CreateRole {
	
	public function __construct($command)
	{
		$role = new Role;
		$role->create($command->attributes);
	}

}