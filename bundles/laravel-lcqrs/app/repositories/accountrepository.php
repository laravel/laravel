<?php namespace Jackzz\Repositories;

use Jackzz\Entities\Account;

class AccountRepository {

	public function get($uuid)
	{
		return DB::table('accounts')->where_id($uuid)->first();;
	}

}