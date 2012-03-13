<?php namespace Jackzz\CommandHandlers;

use Jackzz\AggregateRoots\Account;

class RegisterAccount {

	public function __construct($command)
	{
		$account = new Account;
		$account->command($command->attributes);
	}

}