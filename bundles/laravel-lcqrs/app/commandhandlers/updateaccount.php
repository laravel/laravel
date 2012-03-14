<?php namespace App\CommandHandlers;

use App\AggregateRoots\Account;

class UpdateAccount {
	
	public function __construct($command)
	{
		$account = new Account;
		$account->update($command->attributes);
	}

}