<?php namespace Jackzz\CommandHandlers;

use Jackzz\AggregateRoots\Account;

class UpdateAccount {
	
	public function __construct($command)
	{
		$account = new Account;
		$account->update($command->attributes);
	}

}