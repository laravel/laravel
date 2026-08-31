<?php

namespace Illuminate\Contracts\Translation;

interface Translator
{
    /**
     * Get the translation for a given key.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return mixed
     */
    public function get($key, array $replace = [], $locale = null);

    /**
     * Get a translation according to an integer value.
     *
     * @param  string  $key
     * @param  \Countable|int|float|array  $number
     * @param  array  $replace
     * @param  string|null  $locale
     * @return string
     */
    public function choice($key, $number, array $replace = [], $locale = null);

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set the default locale.
     *
     * @param  string  $locale
     * @return void
     */
    public function setLocale($locale);
}
