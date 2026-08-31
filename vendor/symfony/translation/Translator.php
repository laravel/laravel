<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation;

use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheFactoryInterface;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Exception\RuntimeException;
use Symfony\Component\Translation\Formatter\IntlFormatterInterface;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

// Help opcache.preload discover always-needed symbols
class_exists(MessageCatalogue::class);

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Translator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface
{
    /**
     * @var MessageCatalogueInterface[]
     */
    protected array $catalogues = [];

    private string $locale;

    /**
     * @var string[]
     */
    private array $fallbackLocales = [];

    /**
     * @var LoaderInterface[]
     */
    private array $loaders = [];

    private array $resources = [];

    private MessageFormatterInterface $formatter;

    private ?ConfigCacheFactoryInterface $configCacheFactory;

    private array $parentLocales;

    private bool $hasIntlFormatter;

    /**
     * @var array<string, string|int|float|TranslatableInterface>
     */
    private array $globalParameters = [];

    /**
     * @var array<string, string|int|float>
     */
    private array $globalTranslatedParameters = [];

    /**
     * @throws InvalidArgumentException If a locale contains invalid characters
     */
    public function __construct(
        string $locale,
        ?MessageFormatterInterface $formatter = null,
        private ?string $cacheDir = null,
        private bool $debug = false,
        private array $cacheVary = [],
    ) {
        $this->setLocale($locale);

        $this->formatter = $formatter ??= new MessageFormatter();
        $this->hasIntlFormatter = $formatter instanceof IntlFormatterInterface;
    }

    public function setConfigCacheFactory(ConfigCacheFactoryInterface $configCacheFactory): void
    {
        $this->configCacheFactory = $configCacheFactory;
    }

    /**
     * Adds a Loader.
     *
     * @param string $format The name of the loader (@see addResource())
     */
    public function addLoader(string $format, LoaderInterface $loader): void
    {
        $this->loaders[$format] = $loader;
    }

    /**
     * Adds a Resource.
     *
     * @param string $format   The name of the loader (@see addLoader())
     * @param mixed  $resource The resource name
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     */
    public function addResource(string $format, mixed $resource, string $locale, ?string $domain = null): void
    {
        $domain ??= 'messages';

        $this->assertValidLocale($locale);
        $locale ?: $locale = class_exists(\Locale::class) ? \Locale::getDefault() : 'en';

        $this->resources[$locale][] = [$format, $resource, $domain];

        if (\in_array($locale, $this->fallbackLocales, true)) {
            $this->catalogues = [];
        } else {
            unset($this->catalogues[$locale]);
        }
    }

    public function setLocale(string $locale): void
    {
        $this->assertValidLocale($locale);
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale ?: (class_exists(\Locale::class) ? \Locale::getDefault() : 'en');
    }

    /**
     * Sets the fallback locales.
     *
     * @param string[] $locales
     *
     * @throws InvalidArgumentException If a locale contains invalid characters
     */
    public function setFallbackLocales(array $locales): void
    {
        // needed as the fallback locales are linked to the already loaded catalogues
        $this->catalogues = [];

        foreach ($locales as $locale) {
            $this->assertValidLocale($locale);
        }

        $this->fallbackLocales = $this->cacheVary['fallback_locales'] = $locales;
    }

    /**
     * Gets the fallback locales.
     *
     * @internal
     */
    public function getFallbackLocales(): array
    {
        return $this->fallbackLocales;
    }

    public function addGlobalParameter(string $id, string|int|float|TranslatableInterface $value): void
    {
        $this->globalParameters[$id] = $value;
        $this->globalTranslatedParameters = [];
    }

    public function getGlobalParameters(): array
    {
        return $this->globalParameters;
    }

    public function trans(?string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        if (null === $id || '' === $id) {
            return '';
        }

        $domain ??= 'messages';

        $catalogue = $this->getCatalogue($locale);
        $locale = $catalogue->getLocale();
        while (!$catalogue->defines($id, $domain)) {
            if ($cat = $catalogue->getFallbackCatalogue()) {
                $catalogue = $cat;
                $locale = $catalogue->getLocale();
            } else {
                break;
            }
        }

        foreach ($parameters as $key => $value) {
            if ($value instanceof TranslatableInterface) {
                $parameters[$key] = $value->trans($this, $locale);
            }
        }

        if (null === $globalParameters = &$this->globalTranslatedParameters[$locale]) {
            $globalParameters = $this->globalParameters;
            foreach ($globalParameters as $key => $value) {
                if ($value instanceof TranslatableInterface) {
                    $globalParameters[$key] = $value->trans($this, $locale);
                }
            }
        }

        if ($globalParameters) {
            $parameters += $globalParameters;
        }

        $len = \strlen(MessageCatalogue::INTL_DOMAIN_SUFFIX);
        if ($this->hasIntlFormatter
            && ($catalogue->defines($id, $domain.MessageCatalogue::INTL_DOMAIN_SUFFIX)
            || (\strlen($domain) > $len && 0 === substr_compare($domain, MessageCatalogue::INTL_DOMAIN_SUFFIX, -$len, $len)))
        ) {
            return $this->formatter->formatIntl($catalogue->get($id, $domain), $locale, $parameters);
        }

        return $this->formatter->format($catalogue->get($id, $domain), $locale, $parameters);
    }

    public function getCatalogue(?string $locale = null): MessageCatalogueInterface
    {
        if (!$locale) {
            $locale = $this->getLocale();
        } else {
            $this->assertValidLocale($locale);
        }

        if (!isset($this->catalogues[$locale])) {
            $this->loadCatalogue($locale);
        }

        return $this->catalogues[$locale];
    }

    public function getCatalogues(): array
    {
        return array_values($this->catalogues);
    }

    /**
     * Gets the loaders.
     *
     * @return LoaderInterface[]
     */
    protected function getLoaders(): array
    {
        return $this->loaders;
    }

    protected function loadCatalogue(string $locale): void
    {
        if (null === $this->cacheDir) {
            $this->initializeCatalogue($locale);
        } else {
            $this->initializeCacheCatalogue($locale);
        }
    }

    protected function initializeCatalogue(string $locale): void
    {
        $this->assertValidLocale($locale);

        try {
            $this->doLoadCatalogue($locale);
        } catch (NotFoundResourceException $e) {
            if (!$this->computeFallbackLocales($locale)) {
                throw $e;
            }
        }
        $this->loadFallbackCatalogues($locale);
    }

    private function initializeCacheCatalogue(string $locale): void
    {
        if (isset($this->catalogues[$locale])) {
            /* Catalogue already initialized. */
            return;
        }

        $this->assertValidLocale($locale);
        $cache = $this->getConfigCacheFactory()->cache($this->getCatalogueCachePath($locale),
            function (ConfigCacheInterface $cache) use ($locale) {
                $this->dumpCatalogue($locale, $cache);
            }
        );

        if (isset($this->catalogues[$locale])) {
            /* Catalogue has been initialized as it was written out to cache. */
            return;
        }

        /* Read catalogue from cache. */
        $this->catalogues[$locale] = include $cache->getPath();
    }

    private function dumpCatalogue(string $locale, ConfigCacheInterface $cache): void
    {
        $this->initializeCatalogue($locale);
        $fallbackContent = $this->getFallbackContent($this->catalogues[$locale]);

        $content = \sprintf(<<<EOF
            <?php

            use Symfony\Component\Translation\MessageCatalogue;

            \$catalogue = new MessageCatalogue('%s', %s);

            %s
            return \$catalogue;

            EOF,
            $locale,
            var_export($this->getAllMessages($this->catalogues[$locale]), true),
            $fallbackContent
        );

        $cache->write($content, $this->catalogues[$locale]->getResources());
    }

    private function getFallbackContent(MessageCatalogue $catalogue): string
    {
        $fallbackContent = '';
        $current = '';
        $replacementPattern = '/[^a-z0-9_]/i';
        $fallbackCatalogue = $catalogue->getFallbackCatalogue();
        while ($fallbackCatalogue) {
            $fallback = $fallbackCatalogue->getLocale();
            $fallbackSuffix = ucfirst(preg_replace($replacementPattern, '_', $fallback));
            $currentSuffix = ucfirst(preg_replace($replacementPattern, '_', $current));

            $fallbackContent .= \sprintf(<<<'EOF'
                $catalogue%s = new MessageCatalogue('%s', %s);
                $catalogue%s->addFallbackCatalogue($catalogue%s);

                EOF,
                $fallbackSuffix,
                $fallback,
                var_export($this->getAllMessages($fallbackCatalogue), true),
                $currentSuffix,
                $fallbackSuffix
            );
            $current = $fallbackCatalogue->getLocale();
            $fallbackCatalogue = $fallbackCatalogue->getFallbackCatalogue();
        }

        return $fallbackContent;
    }

    private function getCatalogueCachePath(string $locale): string
    {
        return $this->cacheDir.'/catalogue.'.$locale.'.'.strtr(substr(base64_encode(hash('xxh128', serialize($this->cacheVary), true)), 0, 7), '/', '_').'.php';
    }

    /**
     * @internal
     */
    protected function doLoadCatalogue(string $locale): void
    {
        $this->catalogues[$locale] = new MessageCatalogue($locale);

        if (isset($this->resources[$locale])) {
            foreach ($this->resources[$locale] as $resource) {
                if (!isset($this->loaders[$resource[0]])) {
                    if (\is_string($resource[1])) {
                        throw new RuntimeException(\sprintf('No loader is registered for the "%s" format when loading the "%s" resource.', $resource[0], $resource[1]));
                    }

                    throw new RuntimeException(\sprintf('No loader is registered for the "%s" format.', $resource[0]));
                }
                $this->catalogues[$locale]->addCatalogue($this->loaders[$resource[0]]->load($resource[1], $locale, $resource[2]));
            }
        }
    }

    private function loadFallbackCatalogues(string $locale): void
    {
        $current = $this->catalogues[$locale];

        foreach ($this->computeFallbackLocales($locale) as $fallback) {
            if (!isset($this->catalogues[$fallback])) {
                $this->initializeCatalogue($fallback);
            }

            $fallbackCatalogue = new MessageCatalogue($fallback, $this->getAllMessages($this->catalogues[$fallback]));
            foreach ($this->catalogues[$fallback]->getResources() as $resource) {
                $fallbackCatalogue->addResource($resource);
            }
            $current->addFallbackCatalogue($fallbackCatalogue);
            $current = $fallbackCatalogue;
        }
    }

    protected function computeFallbackLocales(string $locale): array
    {
        $this->parentLocales ??= json_decode(file_get_contents(__DIR__.'/Resources/data/parents.json'), true);

        $originLocale = $locale;
        $locales = [];

        while ($locale) {
            $parent = $this->parentLocales[$locale] ?? null;

            if ($parent) {
                $locale = 'root' !== $parent ? $parent : null;
            } elseif (\function_exists('locale_parse')) {
                $localeSubTags = locale_parse($locale);
                $locale = null;
                if (1 < \count($localeSubTags)) {
                    array_pop($localeSubTags);
                    $locale = locale_compose($localeSubTags) ?: null;
                }
            } elseif ($i = strrpos($locale, '_') ?: strrpos($locale, '-')) {
                $locale = substr($locale, 0, $i);
            } else {
                $locale = null;
            }

            if (null !== $locale) {
                $locales[] = $locale;
            }
        }

        foreach ($this->fallbackLocales as $fallback) {
            if ($fallback === $originLocale) {
                continue;
            }

            $locales[] = $fallback;
        }

        return array_unique($locales);
    }

    /**
     * Asserts that the locale is valid, throws an Exception if not.
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     */
    protected function assertValidLocale(string $locale): void
    {
        if (!preg_match('/^[a-z0-9@_\\.\\-]*$/i', $locale)) {
            throw new InvalidArgumentException(\sprintf('Invalid "%s" locale.', $locale));
        }
    }

    /**
     * Provides the ConfigCache factory implementation, falling back to a
     * default implementation if necessary.
     */
    private function getConfigCacheFactory(): ConfigCacheFactoryInterface
    {
        $this->configCacheFactory ??= new ConfigCacheFactory($this->debug);

        return $this->configCacheFactory;
    }

    private function getAllMessages(MessageCatalogueInterface $catalogue): array
    {
        $allMessages = [];

        foreach ($catalogue->all() as $domain => $messages) {
            if ($intlMessages = $catalogue->all($domain.MessageCatalogue::INTL_DOMAIN_SUFFIX)) {
                $allMessages[$domain.MessageCatalogue::INTL_DOMAIN_SUFFIX] = $intlMessages;
                $messages = array_diff_key($messages, $intlMessages);
            }
            if ($messages) {
                $allMessages[$domain] = $messages;
            }
        }

        return $allMessages;
    }
}
