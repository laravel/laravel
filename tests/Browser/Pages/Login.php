<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class Login extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('session.create');
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
            '@form' => 'form[name="login"]',
            '@register' => 'form[name="login"] a[href="' . route('accounts.create') . '"]',
            '@forgot-password' => 'form[name="login"] a[href="' . route('password-resets.create') . '"]',
            '@submit' => 'form[name="login"] button',
        ];
    }
}
