<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class Register extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('accounts.create');
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
            '@form' => 'form[name="register"]',
            '@email' => 'form[name="register"] input[name="email"]',
            '@login' => 'form[name="register"] a[href="' . route('session.create') . '"]',
            '@forgot-password' => 'form[name="register"] a[href="' . route('password-resets.create') . '"]',
            '@submit' => 'form[name="register"] button',
        ];
    }
}
