<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon;

use Carbon\Exceptions\ImmutableException;
use Symfony\Component\Config\ConfigCacheFactoryInterface;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;

class TranslatorImmutable extends Translator
{
    private bool $constructed = false;

    public function __construct($locale, ?MessageFormatterInterface $formatter = null, $cacheDir = null, $debug = false)
    {
        parent::__construct($locale, $formatter, $cacheDir, $debug);
        $this->constructed = true;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDirectories(array $directories): static
    {
        $this->disallowMutation(__METHOD__);

        return parent::setDirectories($directories);
    }

    public function setLocale($locale): void
    {
        $this->disallowMutation(__METHOD__);

        parent::setLocale($locale);
    }

    /**
     * @codeCoverageIgnore
     */
    public function setMessages(string $locale, array $messages): static
    {
        $this->disallowMutation(__METHOD__);

        return parent::setMessages($locale, $messages);
    }

    /**
     * @codeCoverageIgnore
     */
    public function setTranslations(array $messages): static
    {
        $this->disallowMutation(__METHOD__);

        return parent::setTranslations($messages);
    }

    /**
     * @codeCoverageIgnore
     */
    public function setConfigCacheFactory(ConfigCacheFactoryInterface $configCacheFactory): void
    {
        $this->disallowMutation(__METHOD__);

        parent::setConfigCacheFactory($configCacheFactory);
    }

    public function resetMessages(?string $locale = null): bool
    {
        $this->disallowMutation(__METHOD__);

        return parent::resetMessages($locale);
    }

    /**
     * @codeCoverageIgnore
     */
    public function setFallbackLocales(array $locales): void
    {
        $this->disallowMutation(__METHOD__);

        parent::setFallbackLocales($locales);
    }

    private function disallowMutation($method)
    {
        if ($this->constructed) {
            throw ImmutableException::fromMethod(static::class, $method);
        }
    }
}
