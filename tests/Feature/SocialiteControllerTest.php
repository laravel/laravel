<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

test('socialite redirects - invalid provider', function () {
    // via the controller
    $SocialiteController = new \App\Http\Controllers\SocialiteController;
    $reflection = new \ReflectionClass($SocialiteController);
    $method = $reflection->getMethod('validateProvider');
    $method->setAccessible(true);

    $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
    $method->invoke($SocialiteController, 'foobar');

    // via the route
    $response = $this->get(route('socialite.redirect', ['provider' => 'foobar']));
    $response->assertNotFound();
});

test('socialite redirects - valid provider', function () {
    $this->app['config']->set('services.foobar', [
        'client_id' => Str::random(10),
        'client_secret' => Str::random(10),
    ]);

    // via the controller
    $SocialiteController = new \App\Http\Controllers\SocialiteController;
    $reflection = new \ReflectionClass($SocialiteController);
    $method = $reflection->getMethod('validateProvider');
    $method->setAccessible(true);

    $this->assertNull($method->invoke($SocialiteController, 'foobar'));

    Socialite::shouldReceive('driver')
        ->with('foobar')
        ->andReturnSelf();
    Socialite::shouldReceive('redirect')
        ->andReturn(new Illuminate\Http\RedirectResponse('http://example.com'));

    // via the controller
    $response = $this->get(route('socialite.redirect', ['provider' => 'foobar']));
    $response->assertRedirect();
});

test('socialite callback - invalid provider', function () {
    $response = $this->get(route('socialite.callback', ['provider' => 'foobar']));

    $response->assertNotFound();
});

test('socialite callback - valid provider', function () {
    $this->app['config']->set('services.foobar', [
        'client_id' => Str::random(10),
        'client_secret' => Str::random(10),
    ]);

    $mockSocialite = \Mockery::mock('Laravel\Socialite\Contracts\Factory');
    $this->app['Laravel\Socialite\Contracts\Factory'] = $mockSocialite;

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser
        ->shouldReceive('getId')->andReturn(rand())
        ->shouldReceive('getName')->andReturn(Str::random(10))
        ->shouldReceive('getEmail')->andReturn('email@example.com');

    $provider = Mockery::mock('Laravel\Socialite\Contract\Provider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    $mockSocialite->shouldReceive('driver')->andReturn($provider);

    $response = $this->get(route('socialite.callback', ['provider' => 'foobar']));

    $response->assertRedirect(route('socialite.index'));
    $this->assertAuthenticated();
    $this->assertNotNull(Auth::user());
});

test('logout', function () {
    $this->actingAs(\App\Models\User::factory()->create());

    $response = $this->post(route('socialite.logout'));
    $response->assertRedirect(route('socialite.index'));

    $this->assertGuest();
});
