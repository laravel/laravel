<?php

namespace Illuminate\Foundation\Testing;

use Faker\Factory;
use Faker\Generator;

trait WithFaker
{
    /**
     * The Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Setup up the Faker instance.
     *
     * @return void
     */
    protected function setUpFaker()
    {
        $this->faker = $this->makeFaker();
    }

    /**
     * Get the default Faker instance for a given locale.
     *
     * @param  string|null  $locale
     * @return \Faker\Generator
     */
    protected function faker($locale = null)
    {
        return is_null($locale) ? $this->faker : $this->makeFaker($locale);
    }

    /**
     * Create a Faker instance for the given locale.
     *
     * @param  string|null  $locale
     * @return \Faker\Generator
     */
    protected function makeFaker($locale = null)
    {
        if (isset($this->app)) {
            $locale ??= $this->app->make('config')->get('app.faker_locale', Factory::DEFAULT_LOCALE);

            if ($this->app->bound(Generator::class)) {
                return $this->app->make(Generator::class, ['locale' => $locale]);
            }
        }

        return Factory::create($locale ?? Factory::DEFAULT_LOCALE);
    }
}
