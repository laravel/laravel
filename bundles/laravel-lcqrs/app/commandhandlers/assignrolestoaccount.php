<?php namespace App\CommandHandlers;

use App\AggregateRoots\Account;

class AssignRolesToAccount {
	
	public function __construct($command)
	{
		$account = new Account;
		$account->assign_roles($command->attributes);
	}

}