<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Provider;

use Symfony\Component\Translation\TranslatorBag;
use Symfony\Component\Translation\TranslatorBagInterface;

/**
 * Filters domains and locales between the Translator config values and those specific to each provider.
 *
 * @author Mathieu Santostefano <msantostefano@protonmail.com>
 */
class FilteringProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private array $locales,
        private array $domains = [],
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->provider;
    }

    public function write(TranslatorBagInterface $translatorBag): void
    {
        $this->provider->write($translatorBag);
    }

    public function read(array $domains, array $locales): TranslatorBag
    {
        $domains = !$this->domains ? $domains : array_intersect($this->domains, $domains);
        $locales = array_intersect($this->locales, $locales);

        return $this->provider->read($domains, $locales);
    }

    public function delete(TranslatorBagInterface $translatorBag): void
    {
        $this->provider->delete($translatorBag);
    }

    public function getDomains(): array
    {
        return $this->domains;
    }
}
