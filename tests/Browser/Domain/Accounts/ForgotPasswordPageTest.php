<?php

namespace Tests\Browser\Domain\Accounts;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Domain\Accounts\Account;

class ForgotPasswordPageTest extends DuskTestCase
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
                ->visit(new Pages\ForgotPassword)
                ->assertInputValue('@email', $expected);
        });
    }

    public function test_HelperLinks()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit(new Pages\ForgotPassword)
                ->assertSeeIn('@register', trans('accounts.forgot_password.register'))
                ->press('@register')
                ->on(new Pages\Register);
        });

        $this->browse(function (Browser $browser) {
            $browser
                ->visit(new Pages\ForgotPassword)
                ->assertSeeIn('@login', trans('accounts.forgot_password.login'))
                ->press('@login')
                ->on(new Pages\Login);
        });
    }
}
