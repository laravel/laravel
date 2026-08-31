<?php

namespace Illuminate\Foundation\Events;

class LocaleUpdated
{
    /**
     * The new locale.
     *
     * @var string
     */
    public $locale;

    /**
     * The previous locale.
     *
     * @var ?string
     */
    public $previousLocale;

    /**
     * Create a new event instance.
     *
     * @param  string  $locale
     * @param  ?string  $previousLocale
     */
    public function __construct($locale, $previousLocale = null)
    {
        $this->locale = $locale;

        $this->previousLocale = $previousLocale;
    }
}
