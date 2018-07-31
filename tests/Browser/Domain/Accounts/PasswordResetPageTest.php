<?php

namespace Tests\Browser\Domain\Accounts;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Domain\Accounts\Account;

class PasswordResetPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_ResetsPassword()
    {
        $this->browse(function (Browser $browser) {
            $account = factory(Account::class)->create();

            DB::table('password_resets')->insert([
                'email' => $account->email,
                'token' => Hash::make($token = 'secret'),
                'created_at' => now(),
            ]);

            $browser
                ->visit(new Pages\PasswordReset($token))
                ->type('email', $account->email)
                ->type('password', $password = 'different')
                ->type('password_confirmation', 'different')
                ->press('@submit')
                ->waitForReload()
                ->visit(new Pages\Login)
                ->type('email', $account->email)
                ->type('password', $password)
                ->press('@submit')
                ->waitForReload()
                ->assertAuthenticatedAs($account);
        });
    }
}
