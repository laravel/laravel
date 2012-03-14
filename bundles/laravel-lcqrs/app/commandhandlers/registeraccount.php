<?php namespace App\CommandHandlers;

use App\AggregateRoots\Account;

class RegisterAccount {

	public function __construct($command)
	{
		$account = new Account;
		$account->command($command->attributes);
	}

}