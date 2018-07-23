<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class ForgotPassword extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('password-resets.create');
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
            '@form' => 'form[name="forgot-password"]',
            '@email' => 'form[name="forgot-password"] input[name="email"]',
            '@register' => 'form[name="forgot-password"] a[href="' . route('accounts.create') . '"]',
            '@login' => 'form[name="forgot-password"] a[href="' . route('session.create') . '"]',
            '@submit' => 'form[name="forgot-password"] button',
        ];
    }
}
