<?php

namespace App\Domain\Accounts\Verification\Events;

use App\Domain\Accounts\Account;

class AccountVerified
{
    /**
     * @var \App\Domain\Accounts\Account
     */
    public $account;

    /**
     * Creates a new AccountVerified event
     *
     * @param \App\Domain\Accounts\Account $account
     * 
     * @return void
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }
}
