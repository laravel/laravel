<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use App\Domain\Accounts\Account;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Creates and authenticates as a consented Account.
     *
     * @return Account
     */
    protected function register()
    {
        $account = factory(Account::class)->create();

        $this->actingAs($account);

        return $account;
    }
}
