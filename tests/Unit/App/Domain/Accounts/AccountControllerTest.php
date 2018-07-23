<?php

namespace Tests\Unit\App\Domain\Accounts;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

use Tests\TestCase;
use App\Domain\Accounts\Account;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_ReturnsRegisterForm()
    {
        // Given
        $this->assertGuest();

        // When
        $response = $this->get(route('accounts.create'));

        // Then
        $response->assertStatus(200);
    }

    public function test_create_GivenAuthenticated_RedirectsToHomePage()
    {
        // Given
        $this->register();

        // When
        $response = $this->get(route('accounts.create'));

        // Then
        $response->assertRedirect(route('home.show'));
    }

    public function test_create_GivenLoggedIn_RedirectsToHome()
    {
        // Given
        $this->register();

        // When
        $response = $this->get(route('accounts.create'));

        // Then
        $response->assertRedirect(route('home.show'));
    }

    public function test_store_GivenNoValues_ReturnsUnprocessableEntity()
    {
        // Given When
        $response = $this->json('post', route('accounts.store'));

        // Then
        $response->assertStatus(422);
    }

    public function test_store_GivenNoValues_ReturnsErrorsForRequiredKeys()
    {
        // Given When
        $response = $this->json('post', route('accounts.store'));

        // Then
        $response->assertJsonValidationErrors([ 'name', 'email', 'password' ]);
    }

    public function test_store_ReturnsJsonRedirect()
    {
        // Given
        $body = factory(Account::class)->states('unregistered')->raw();

        // When
        $response = $this->json('post', route('accounts.store'), $body);

        // Then
        $response->assertJson([ 'redirect' => route('session.create') ]);
    }

    public function test_store_FlashesVerifyMessage()
    {
        $this->withoutExceptionHandling();

        // Given
        $body = factory(Account::class)->states('unregistered')->raw();

        // When
        $response = $this->json('post', route('accounts.store'), $body);

        // Then
        $response->assertJson([ 'redirect' => route('session.create') ]);
        $response->assertSessionHas('message', trans('accounts.verification.sent'));
    }

    public function test_store_AccountIsCreated()
    {
        // Given
        $body = factory(Account::class)->states('unregistered')->raw();

        // When
        $this->json('post', route('accounts.store'), $body);

        // Then
        $this->assertEquals($body['name'], Account::where('email', $body['email'])->first()->name);
    }

    public function test_store_DoesNotAuthenticate()
    {
        // Given
        $body = factory(Account::class)->states('unregistered')->raw();

        // When
        $this->json('post', route('accounts.store'), $body);

        // Then
        $this->assertGuest();
    }

    public function test_store_RegisteredEventFires()
    {
        // Given
        $body = factory(Account::class)->states('unregistered')->raw();

        // When
        Event::fake();
        $this->json('post', route('accounts.store'), $body);

        // Then
        Event::assertDispatched(Registered::class);
    }

    public function test_store_GivenEmailAlreadyExists_ReturnsJsonRedirectAndFlashes()
    {
        // Given
        $existing = factory(Account::class)->create();
        $body = factory(Account::class)->states('unregistered')->raw($existing->only('email'));

        // When
        $response = $this->json('post', route('accounts.store'), $body);

        // Then
        $response->assertJson([ 'redirect' => route('session.create') ]);
        $response->assertSessionHas('message', trans('accounts.verification.sent'));

    }

    public function test_store_GivenEmailAlreadyExists_DoesNotCreateAccount()
    {
        // Given
        $existing = factory(Account::class)->create();
        $body = factory(Account::class)->states('unregistered')->raw($existing->only('email'));

        // When
        Event::fake();
        $response = $this->json('post', route('accounts.store'), $body);

        // Then
        $this->assertCount(1, Account::all());
        Event::assertNotDispatched(Registered::class);
    }
}
