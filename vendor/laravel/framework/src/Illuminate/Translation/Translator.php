<?php

namespace Illuminate\Translation;

use Closure;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Support\Arr;
use Illuminate\Support\NamespacedItemResolver;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\ReflectsClosures;
use InvalidArgumentException;

use function Illuminate\Support\enum_value;

class Translator extends NamespacedItemResolver implements TranslatorContract
{
    use Macroable, ReflectsClosures;

    /**
     * The loader implementation.
     *
     * @var \Illuminate\Contracts\Translation\Loader
     */
    protected $loader;

    /**
     * The default locale being used by the translator.
     *
     * @var string
     */
    protected $locale;

    /**
     * The fallback locale used by the translator.
     *
     * @var string
     */
    protected $fallback;

    /**
     * The array of loaded translation groups.
     *
     * @var array
     */
    protected $loaded = [];

    /**
     * The message selector.
     *
     * @var \Illuminate\Translation\MessageSelector
     */
    protected $selector;

    /**
     * The callable that should be invoked to determine applicable locales.
     *
     * @var callable
     */
    protected $determineLocalesUsing;

    /**
     * The custom rendering callbacks for stringable objects.
     *
     * @var array
     */
    protected $stringableHandlers = [];

    /**
     * The callback that is responsible for handling missing translation keys.
     *
     * @var callable|null
     */
    protected $missingTranslationKeyCallback;

    /**
     * Indicates whether missing translation keys should be handled.
     *
     * @var bool
     */
    protected $handleMissingTranslationKeys = true;

    /**
     * Create a new translator instance.
     *
     * @param  \Illuminate\Contracts\Translation\Loader  $loader
     * @param  string  $locale
     */
    public function __construct(Loader $loader, $locale)
    {
        $this->loader = $loader;

        $this->setLocale($locale);
    }

    /**
     * Determine if a translation exists for a given locale.
     *
     * @param  string  $key
     * @param  string|null  $locale
     * @return bool
     */
    public function hasForLocale($key, $locale = null)
    {
        return $this->has($key, $locale, false);
    }

    /**
     * Determine if a translation exists.
     *
     * @param  string  $key
     * @param  string|null  $locale
     * @param  bool  $fallback
     * @return bool
     */
    public function has($key, $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->locale;

        // We should temporarily disable the handling of missing translation keys
        // while performing the existence check. After the check, we will turn
        // the missing translation keys handling back to its original value.
        $handleMissingTranslationKeys = $this->handleMissingTranslationKeys;

        $this->handleMissingTranslationKeys = false;

        $line = $this->get($key, [], $locale, $fallback);

        $this->handleMissingTranslationKeys = $handleMissingTranslationKeys;

        // For JSON translations, the loaded files will contain the correct line.
        // Otherwise, we must assume we are handling typical translation file
        // and check if the returned line is not the same as the given key.
        if (! is_null($this->loaded['*']['*'][$locale][$key] ?? null)) {
            return true;
        }

        return $line !== $key;
    }

    /**
     * Get the translation for the given key.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @param  bool  $fallback
     * @return string|array
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->locale;

        // For JSON translations, there is only one file per locale, so we will simply load
        // that file and then we will be ready to check the array for the key. These are
        // only one level deep so we do not need to do any fancy searching through it.
        $this->load('*', '*', $locale);

        $line = $this->loaded['*']['*'][$locale][$key] ?? null;

        // If we can't find a translation for the JSON key, we will attempt to translate it
        // using the typical translation file. This way developers can always just use a
        // helper such as __ instead of having to pick between trans or __ with views.
        if (! isset($line)) {
            [$namespace, $group, $item] = $this->parseKey($key);

            // Here we will get the locale that should be used for the language line. If one
            // was not passed, we will use the default locales which was given to us when
            // the translator was instantiated. Then, we can load the lines and return.
            $locales = $fallback ? $this->localeArray($locale) : [$locale];

            foreach ($locales as $languageLineLocale) {
                if (! is_null($line = $this->getLine(
                    $namespace, $group, $languageLineLocale, $item, $replace
                ))) {
                    return $line;
                }
            }

            $key = $this->handleMissingTranslationKey(
                $key, $replace, $locale, $fallback
            );
        }

        // If the line doesn't exist, we will return back the key which was requested as
        // that will be quick to spot in the UI if language keys are wrong or missing
        // from the application's language files. Otherwise we can return the line.
        return $this->makeReplacements($line ?: $key, $replace);
    }

    /**
     * Get a translation according to an integer value.
     *
     * @param  string  $key
     * @param  \Countable|int|float|array  $number
     * @param  array  $replace
     * @param  string|null  $locale
     * @return string
     */
    public function choice($key, $number, array $replace = [], $locale = null)
    {
        $line = $this->get(
            $key, [], $locale = $this->localeForChoice($key, $locale)
        );

        // If the given "number" is actually an array or countable we will simply count the
        // number of elements in an instance. This allows developers to pass an array of
        // items without having to count it on their end first which gives bad syntax.
        if (is_countable($number)) {
            $number = count($number);
        }

        if (! isset($replace['count'])) {
            $replace['count'] = $number;
        }

        return $this->makeReplacements(
            $this->getSelector()->choose($line, $number, $locale), $replace
        );
    }

    /**
     * Get the proper locale for a choice operation.
     *
     * @param  string  $key
     * @param  string|null  $locale
     * @return string
     */
    protected function localeForChoice($key, $locale)
    {
        $locale = $locale ?: $this->locale;

        return $this->hasForLocale($key, $locale) ? $locale : $this->fallback;
    }

    /**
     * Retrieve a language line out the loaded array.
     *
     * @param  string  $namespace
     * @param  string  $group
     * @param  string  $locale
     * @param  string  $item
     * @param  array  $replace
     * @return string|array|null
     */
    protected function getLine($namespace, $group, $locale, $item, array $replace)
    {
        $this->load($namespace, $group, $locale);

        $line = Arr::get($this->loaded[$namespace][$group][$locale], $item);

        if (is_string($line)) {
            return $this->makeReplacements($line, $replace);
        } elseif (is_array($line) && count($line) > 0) {
            array_walk_recursive($line, function (&$value, $key) use ($replace) {
                $value = $this->makeReplacements($value, $replace);
            });

            return $line;
        }
    }

    /**
     * Make the place-holder replacements on a line.
     *
     * @param  string  $line
     * @param  array  $replace
     * @return string
     */
    protected function makeReplacements($line, array $replace)
    {
        if (empty($replace)) {
            return $line;
        }

        $shouldReplace = [];

        foreach ($replace as $key => $value) {
            if ($value instanceof Closure) {
                $line = preg_replace_callback(
                    '/<'.$key.'>(.*?)<\/'.$key.'>/',
                    fn ($args) => $value($args[1]),
                    $line
                );

                continue;
            }

            if (is_object($value)) {
                $value = isset($this->stringableHandlers[get_class($value)])
                    ? call_user_func($this->stringableHandlers[get_class($value)], $value)
                    : enum_value($value);
            }

            $shouldReplace[':'.Str::ucfirst($key)] = Str::ucfirst($value ?? '');
            $shouldReplace[':'.Str::upper($key)] = Str::upper($value ?? '');
            $shouldReplace[':'.$key] = $value;
        }

        return strtr($line, $shouldReplace);
    }

    /**
     * Add translation lines to the given locale.
     *
     * @param  array  $lines
     * @param  string  $locale
     * @param  string  $namespace
     * @return void
     */
    public function addLines(array $lines, $locale, $namespace = '*')
    {
        foreach ($lines as $key => $value) {
            [$group, $item] = explode('.', $key, 2);

            Arr::set($this->loaded, "$namespace.$group.$locale.$item", $value);
        }
    }

    /**
     * Load the specified language group.
     *
     * @param  string  $namespace
     * @param  string  $group
     * @param  string  $locale
     * @return void
     */
    public function load($namespace, $group, $locale)
    {
        if ($this->isLoaded($namespace, $group, $locale)) {
            return;
        }

        // The loader is responsible for returning the array of language lines for the
        // given namespace, group, and locale. We'll set the lines in this array of
        // lines that have already been loaded so that we can easily access them.
        $lines = $this->loader->load($locale, $group, $namespace);

        $this->loaded[$namespace][$group][$locale] = $lines;
    }

    /**
     * Determine if the given group has been loaded.
     *
     * @param  string  $namespace
     * @param  string  $group
     * @param  string  $locale
     * @return bool
     */
    protected function isLoaded($namespace, $group, $locale)
    {
        return isset($this->loaded[$namespace][$group][$locale]);
    }

    /**
     * Handle a missing translation key.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @param  bool  $fallback
     * @return string
     */
    protected function handleMissingTranslationKey($key, $replace, $locale, $fallback)
    {
        if (! $this->handleMissingTranslationKeys ||
            ! isset($this->missingTranslationKeyCallback)) {
            return $key;
        }

        // Prevent infinite loops...
        $this->handleMissingTranslationKeys = false;

        $key = call_user_func(
            $this->missingTranslationKeyCallback,
            $key, $replace, $locale, $fallback
        ) ?? $key;

        $this->handleMissingTranslationKeys = true;

        return $key;
    }

    /**
     * Register a callback that is responsible for handling missing translation keys.
     *
     * @param  callable|null  $callback
     * @return static
     */
    public function handleMissingKeysUsing(?callable $callback)
    {
        $this->missingTranslationKeyCallback = $callback;

        return $this;
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        $this->loader->addNamespace($namespace, $hint);
    }

    /**
     * Add a new path to the loader.
     *
     * @param  string  $path
     * @return void
     */
    public function addPath($path)
    {
        $this->loader->addPath($path);
    }

    /**
     * Add a new JSON path to the loader.
     *
     * @param  string  $path
     * @return void
     */
    public function addJsonPath($path)
    {
        $this->loader->addJsonPath($path);
    }

    /**
     * Parse a key into namespace, group, and item.
     *
     * @param  string  $key
     * @return array
     */
    public function parseKey($key)
    {
        $segments = parent::parseKey($key);

        if (is_null($segments[0])) {
            $segments[0] = '*';
        }

        return $segments;
    }

    /**
     * Get the array of locales to be checked.
     *
     * @param  string|null  $locale
     * @return array
     */
    protected function localeArray($locale)
    {
        $locales = array_filter([$locale ?: $this->locale, $this->fallback]);

        $determined = call_user_func($this->determineLocalesUsing ?: fn () => $locales, $locales);

        return array_values(array_unique($determined));
    }

    /**
     * Specify a callback that should be invoked to determined the applicable locale array.
     *
     * @param  callable  $callback
     * @return void
     */
    public function determineLocalesUsing($callback)
    {
        $this->determineLocalesUsing = $callback;
    }

    /**
     * Get the message selector instance.
     *
     * @return \Illuminate\Translation\MessageSelector
     */
    public function getSelector()
    {
        if (! isset($this->selector)) {
            $this->selector = new MessageSelector;
        }

        return $this->selector;
    }

    /**
     * Set the message selector instance.
     *
     * @param  \Illuminate\Translation\MessageSelector  $selector
     * @return void
     */
    public function setSelector(MessageSelector $selector)
    {
        $this->selector = $selector;
    }

    /**
     * Get the language line loader implementation.
     *
     * @return \Illuminate\Contracts\Translation\Loader
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function locale()
    {
        return $this->getLocale();
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the default locale.
     *
     * @param  string  $locale
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function setLocale($locale)
    {
        if (Str::contains($locale, ['/', '\\'])) {
            throw new InvalidArgumentException('Invalid characters present in locale.');
        }

        $this->locale = $locale;
    }

    /**
     * Get the fallback locale being used.
     *
     * @return string
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * Set the fallback locale being used.
     *
     * @param  string  $fallback
     * @return void
     */
    public function setFallback($fallback)
    {
        $this->fallback = $fallback;
    }

    /**
     * Set the loaded translation groups.
     *
     * @param  array  $loaded
     * @return void
     */
    public function setLoaded(array $loaded)
    {
        $this->loaded = $loaded;
    }

    /**
     * Add a handler to be executed in order to format a given class to a string during translation replacements.
     *
     * @param  callable|string  $class
     * @param  callable|null  $handler
     * @return void
     */
    public function stringable($class, $handler = null)
    {
        if ($class instanceof Closure) {
            [$class, $handler] = [
                $this->firstClosureParameterType($class),
                $class,
            ];
        }

        $this->stringableHandlers[$class] = $handler;
    }
}
