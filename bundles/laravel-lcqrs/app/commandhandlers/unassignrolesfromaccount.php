<?php namespace App\CommandHandlers;

use App\AggregateRoots\Account;

class UnassignRolesFromAccount {
	
	public function __construct($command)
	{
		$account = new Account;
		$account->unassign_roles($command->attributes);
	}

}