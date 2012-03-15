<?php namespace Jackzz\CommandHandlers;

use Jackzz\AggregateRoots\Account;

class UnassignRolesFromAccount {
	
	public function __construct($command)
	{
		$account = new Account;
		$account->unassign_roles($command->attributes);
	}

}