<?php

namespace Tests\Browser\Domain\Accounts;


use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Tests\DuskTestCase;
use Tests\Browser\Pages;

use App\Domain\Accounts\Account;

class ResendVerifyCodePageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_SendsVerifyCode()
    {
        $this->browse(function (Browser $browser) {
            $account = factory(Account::class)->create();

            $browser
                ->visit(new Pages\ResendVerifyCode)
                ->type('email', $account->email)
                ->press('@submit')
                ->waitFor('.error-text')
                ->visit(new Pages\ForgotPassword)
                ->assertInputValue('@email', $expected);
        });

        $this->assertDatabaseHas('verify_codes', [
            'email' => $user->email,
        ]);
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
