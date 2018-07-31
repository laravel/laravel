<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class ResendVerifyCode extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return route('verify-codes.create');
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
            '@form' => 'form[name="resent-verify-code"]',
            '@email' => 'form[name="resent-verify-code"] input[name="email"]',
            '@register' => 'form[name="resent-verify-code"] a[href="' . route('accounts.create') . '"]',
            '@forgot-password' => 'form[name="resent-verify-code"] a[href="' . route('password-resets.create') . '"]',
            '@submit' => 'form[name="resent-verify-code"] button',
        ];
    }
}
