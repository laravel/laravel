<?php namespace Jackzz\CommandHandlers;

use Jackzz\AggregateRoots\Account;

class CreateAccount {
	
	public function __construct($command)
	{
		$account = new Account;
		$account->create($command->attributes);
	}

}