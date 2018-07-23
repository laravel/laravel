<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class PasswordReset extends Page
{
    /**
     * Token of the current page.
     *
     * @var  string
     */
    protected $token;

    /**
     * Creates an instance of a PasswordReset page.
     *
     * @param  $token  string
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('password-resets.show', $this->token);
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser
            ->assertUrlIs($this->url())
            ->assertVisible('@form');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@form' => 'form[name="password-reset"]',
            '@submit' => 'form[name="password-reset"] button',
        ];
    }
}
