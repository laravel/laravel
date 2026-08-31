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

use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Contracts\Service\ResetInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 *
 * @final since Symfony 7.1
 */
class DataCollectorTranslator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface, WarmableInterface, ResetInterface
{
    public const MESSAGE_DEFINED = 0;
    public const MESSAGE_MISSING = 1;
    public const MESSAGE_EQUALS_FALLBACK = 2;

    private array $messages = [];

    public function __construct(
        private TranslatorInterface&TranslatorBagInterface&LocaleAwareInterface $translator,
    ) {
    }

    public function reset(): void
    {
        $this->messages = [];
    }

    public function trans(?string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        $trans = $this->translator->trans($id = (string) $id, $parameters, $domain, $locale);
        $this->collectMessage($locale, $domain, $id, $trans, $parameters);

        return $trans;
    }

    public function setLocale(string $locale): void
    {
        $this->translator->setLocale($locale);
    }

    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }

    public function getCatalogue(?string $locale = null): MessageCatalogueInterface
    {
        return $this->translator->getCatalogue($locale);
    }

    public function getCatalogues(): array
    {
        return $this->translator->getCatalogues();
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        if ($this->translator instanceof WarmableInterface) {
            return $this->translator->warmUp($cacheDir, $buildDir);
        }

        return [];
    }

    /**
     * Gets the fallback locales.
     */
    public function getFallbackLocales(): array
    {
        if ($this->translator instanceof Translator || method_exists($this->translator, 'getFallbackLocales')) {
            return $this->translator->getFallbackLocales();
        }

        return [];
    }

    public function getGlobalParameters(): array
    {
        if ($this->translator instanceof Translator || method_exists($this->translator, 'getGlobalParameters')) {
            return $this->translator->getGlobalParameters();
        }

        return [];
    }

    public function __call(string $method, array $args): mixed
    {
        return $this->translator->{$method}(...$args);
    }

    public function getCollectedMessages(): array
    {
        return $this->messages;
    }

    private function collectMessage(?string $locale, ?string $domain, string $id, string $translation, ?array $parameters = []): void
    {
        $domain ??= 'messages';

        $catalogue = $this->translator->getCatalogue($locale);
        $locale = $catalogue->getLocale();
        $fallbackLocale = null;
        if ($catalogue->defines($id, $domain)) {
            $state = self::MESSAGE_DEFINED;
        } elseif ($catalogue->has($id, $domain)) {
            $state = self::MESSAGE_EQUALS_FALLBACK;

            $fallbackCatalogue = $catalogue->getFallbackCatalogue();
            while ($fallbackCatalogue) {
                if ($fallbackCatalogue->defines($id, $domain)) {
                    $fallbackLocale = $fallbackCatalogue->getLocale();
                    break;
                }
                $fallbackCatalogue = $fallbackCatalogue->getFallbackCatalogue();
            }
        } else {
            $state = self::MESSAGE_MISSING;
        }

        $this->messages[] = [
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
            'domain' => $domain,
            'id' => $id,
            'translation' => $translation,
            'parameters' => $parameters,
            'state' => $state,
            'transChoiceNumber' => isset($parameters['%count%']) && is_numeric($parameters['%count%']) ? $parameters['%count%'] : null,
        ];
    }
}
