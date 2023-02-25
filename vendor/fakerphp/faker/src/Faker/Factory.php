<?php

namespace Faker;

class Factory
{
    public const DEFAULT_LOCALE = 'en_US';

    protected static $defaultProviders = ['Address', 'Barcode', 'Biased', 'Color', 'Company', 'DateTime', 'File', 'HtmlLorem', 'Image', 'Internet', 'Lorem', 'Medical', 'Miscellaneous', 'Payment', 'Person', 'PhoneNumber', 'Text', 'UserAgent', 'Uuid'];

    /**
     * Create a new generator
     *
     * @param string $locale
     *
     * @return Generator
     */
    public static function create($locale = self::DEFAULT_LOCALE)
    {
        $generator = new Generator();

        foreach (static::$defaultProviders as $provider) {
            $providerClassName = self::getProviderClassname($provider, $locale);
            $generator->addProvider(new $providerClassName($generator));
        }

        return $generator;
    }

    /**
     * @param string $provider
     * @param string $locale
     *
     * @return string
     */
    protected static function getProviderClassname($provider, $locale = '')
    {
        if ($providerClass = self::findProviderClassname($provider, $locale)) {
            return $providerClass;
        }
        // fallback to default locale
        if ($providerClass = self::findProviderClassname($provider, static::DEFAULT_LOCALE)) {
            return $providerClass;
        }
        // fallback to no locale
        if ($providerClass = self::findProviderClassname($provider)) {
            return $providerClass;
        }

        throw new \InvalidArgumentException(sprintf('Unable to find provider "%s" with locale "%s"', $provider, $locale));
    }

    /**
     * @param string $provider
     * @param string $locale
     *
     * @return string|null
     */
    protected static function findProviderClassname($provider, $locale = '')
    {
        $providerClass = 'Faker\\' . ($locale ? sprintf('Provider\%s\%s', $locale, $provider) : sprintf('Provider\%s', $provider));

        if (class_exists($providerClass, true)) {
            return $providerClass;
        }

        return null;
    }
}
