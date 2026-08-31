<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Formatter;

use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;

// Help opcache.preload discover always-needed symbols
class_exists(IntlFormatter::class);

/**
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 */
class MessageFormatter implements MessageFormatterInterface, IntlFormatterInterface
{
    private TranslatorInterface $translator;
    private IntlFormatterInterface $intlFormatter;

    /**
     * @param TranslatorInterface|null $translator An identity translator to use as selector for pluralization
     */
    public function __construct(?TranslatorInterface $translator = null, ?IntlFormatterInterface $intlFormatter = null)
    {
        $this->translator = $translator ?? new IdentityTranslator();
        $this->intlFormatter = $intlFormatter ?? new IntlFormatter();
    }

    public function format(string $message, string $locale, array $parameters = []): string
    {
        return $this->translator->trans($message, $parameters, null, $locale);
    }

    public function formatIntl(string $message, string $locale, array $parameters = []): string
    {
        return $this->intlFormatter->formatIntl($message, $locale, $parameters);
    }
}
