<?php

namespace Tests\Browser\Domain\Accounts;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Domain\Accounts\Account;

class RegisterPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_AfterFailedLoginEmailIsRemembered()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit(new Pages\Login)
                ->type('email', $expected = 'test')
                ->press('@submit')
                ->waitFor('.error-text')
                ->visit(new Pages\Register)
                ->assertInputValue('@email', $expected);
        });
    }

    public function test_HelperLinks()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit(new Pages\Register)
                ->assertSeeIn('@login', trans('accounts.register.login'))
                ->press('@login')
                ->on(new Pages\Login);
        });
    }
}
