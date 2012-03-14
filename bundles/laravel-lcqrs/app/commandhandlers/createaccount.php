<?php namespace App\CommandHandlers;

use App\AggregateRoots\Account;

class CreateAccount {
	
	public function __construct($command)
	{
		$account = new Account;
		$account->create($command->attributes);
	}

}