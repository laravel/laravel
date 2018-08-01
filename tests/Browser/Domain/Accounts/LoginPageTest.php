<?php

namespace Tests\Browser\Domain\Accounts;

use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Tests\DuskTestCase;
use Tests\Browser\Pages;

use App\Domain\Accounts\Account;

class LoginPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_RequiredFields()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit(new Pages\Login)
                ->press('@submit')
                ->waitForText(trans('validation.required', [ 'attribute' => 'email' ]));
        });
    }

    public function test_CanLogin()
    {
        $this->browse(function (Browser $browser) {
            $account = factory(Account::class)->create();

            $browser
                ->visit(new Pages\Login)
                ->type('email', $account->email)
                ->type('password', 'secret')
                ->press('@submit')
                ->waitForReload()
                ->on(new Pages\Home)
                ->assertAuthenticatedAs($account);
        });
    }

    public function test_HelperLinks()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->assertGuest()
                ->visit(new Pages\Login)
                ->assertSeeIn('@forgot-password', trans('accounts.login.forgot_password'))
                ->press('@forgot-password')
                ->on(new Pages\ForgotPassword);
        });

        $this->browse(function (Browser $browser) {
            $browser
                ->visit(new Pages\Login)
                ->assertSeeIn('@register', trans('accounts.login.register'))
                ->press('@register')
                ->on(new Pages\Register);
        });
    }
}
