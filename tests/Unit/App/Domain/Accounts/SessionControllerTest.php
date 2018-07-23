<?php

namespace Tests\Unit\App\Domain\Accounts;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;
use App\Domain\Accounts\Account;

class SessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_ReturnsLoginrForm()
    {
        // Given
        $this->assertGuest();

        // When
        $response = $this->get(route('session.create'));

        // Then
        $response->assertStatus(200);
    }

    public function test_create_GivenAuthenticated_RedirectsToHomePage()
    {
        // Given
        $this->register();

        // When
        $response = $this->get(route('session.create'));

        // Then
        $response->assertRedirect(route('home.show'));
    }

    public function test_create_GivenLoggedIn_RedirectsToHome()
    {
        // Given
        $this->register();

        // When
        $response = $this->get(route('session.create'));

        // Then
        $response->assertRedirect(route('home.show'));
    }

    public function test_store_GivenNoValues_ReturnsUnprocessableEntity()
    {
        // Given When
        $response = $this->json('post', route('session.store'));

        // Then
        $response->assertStatus(422);
    }

    public function test_store_GivenNoValues_ReturnsErrorsForRequiredKeys()
    {
        // Given When
        $response = $this->json('post', route('session.store'));

        // Then
        $response->assertJsonValidationErrors([ 'email', 'password' ]);
    }

    public function test_store_ReturnsJsonRedirect()
    {
        // Given
        $account = factory(Account::class)->create();

        // When
        $response = $this->json('post', route('session.store'), [
            'email' => $account->email,
            'password' => 'secret',
        ]);

        // Then
        $response->assertJson([ 'redirect' => route('home.show') ]);
    }

    public function test_store_Authenticates()
    {
        // Given
        $account = factory(Account::class)->create();

        // When
        $this->json('post', route('session.store'), [
            'email' => $account->email,
            'password' => 'secret',
        ]);

        // Then
        $this->assertAuthenticatedAs($account);
    }

    public function test_store_GivenInvalidPassword_ReturnsValidationErrors()
    {
        // Given
        $account = factory(Account::class)->create();

        // When
        $response = $this->json('post', route('session.store'), [
            'email' => $account->email,
            'password' => 'wrong password',
        ]);

        // Then
        $response->assertJsonValidationErrors([ 'email' ]);
    }

    public function test_store_GivenInvalidEmail_ReturnsValidationErrors()
    {
        // Given
        $account = factory(Account::class)->make();

        // When
        $response = $this->json('post', route('session.store'), [
            'email' => $account->email,
            'password' => 'secret',
        ]);

        // Then
        $response->assertJsonValidationErrors([ 'email' ]);
    }
}
