<?php

namespace Tests\Unit\App\Domain\Accounts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Tests\TestCase;
use App\Domain\Accounts\Account;

class PasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_ReturnsResetForm()
    {
        // Given
        $this->assertGuest();

        // When
        $response = $this->get(route('password-resets.create'));

        // Then
        $response->assertStatus(200);
    }

    public function test_create_GivenAuthenticated_RedirectsToHomePage()
    {
        // Given
        $this->register();

        // When
        $response = $this->get(route('password-resets.create'));

        // Then
        $response->assertRedirect(route('page/home'));
    }

    public function test_store_GivenNoEmail_ReturnsValidationErrors()
    {
        // When
        $response = $this->json('post', route('password-resets.store'));

        // Then
        $response->assertJsonValidationErrors('email');
    }

    public function test_store_GivenInvalidEmail_ReturnsSentMessage()
    {
        // Given
        $account = factory(Account::class)->make();

        // When
        $response = $this->json('post', route('password-resets.store'), $account->only('email'));

        // Then
        $response->assertJson([ 'message' => trans('accounts.passwords.sent') ]);
    }

    public function test_store_GivenValidEmail_ReturnsSentMessage()
    {
        $this->withoutExceptionHandling();

        // Given
        $account = factory(Account::class)->create();

        // When
        $response = $this->json('post', route('password-resets.store'), $account->only('email'));

        // Then
        $response->assertJson([ 'message' => trans('accounts.passwords.sent') ]);
    }

    public function test_show_GivenResetExists_Returns200()
    {
        // Given
        DB::table('password_resets')->insert([
            'email' => factory(Account::class)->create()->email,
            'token' => Hash::make($token = 'secret'),
        ]);

        // When
        $response = $this->get(route('password-resets.show', $token));

        // Then
        $response->assertStatus(200);
    }

    public function test_show_GivenResetDoesntExists_Returns200()
    {
        // Given
        DB::table('password_resets')->insert([
            'email' => factory(Account::class)->create()->email,
            'token' => Hash::make($token = 'secret'),
        ]);

        // When
        $response = $this->get(route('password-resets.show', 'incorrect'));

        // Then
        $response->assertStatus(200);
    }

    public function test_update_GivenResetDoesntExists_ReturnsJsonError()
    {
        // Given
        $password = factory(Account::class)->states('unregistered')->raw()['password'];
        $account = factory(Account::class)->create();

        // When
        $response = $this->json('patch', route('password-resets.show', 'incorrect'), [
            'email' => $account->email,
            'password' => $password = 'password',
            'password_confirmation' => $password,
        ]);

        // Then
        $response->assertJson(['message' => trans('accounts.passwords.token') ]);
        $response->assertStatus(422);
    }

    public function test_update_GivenResetExistsWithIncorrectEmail_ReturnsJsonError()
    {
        // Given
        $password = factory(Account::class)->states('unregistered')->raw()['password'];
        [ $account, $other ] = factory(Account::class, 2)->create();
        DB::table('password_resets')->insert([
            'email' => $account->email,
            'token' => Hash::make($token = 'secret'),
        ]);

        // When
        $response = $this->json('patch', route('password-resets.show', $token), [
            'email' => $other->email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        // Then
        $response->assertJson(['message' => trans('accounts.passwords.token') ]);
        $response->assertStatus(422);
    }

    public function test_update_GivenResetExists_ReturnsJsonRedirectToLogin()
    {
        // Given
        $password = factory(Account::class)->states('unregistered')->raw()['password'];
        $account = factory(Account::class)->create();
        DB::table('password_resets')->insert([
            'email' => $account->email,
            'token' => Hash::make($token = 'secret'),
            'created_at' => now(),
        ]);

        // When
        $response = $this->json('patch', route('password-resets.show', $token), $account->only('email') + [
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        // Then
        $response->assertSessionHas('message', trans('accounts.passwords.reset'));
        $response->assertJson([ 'redirect' => route('session.create') ]);
        $this->assertEmpty(DB::table('password_resets')->get());
    }
}
