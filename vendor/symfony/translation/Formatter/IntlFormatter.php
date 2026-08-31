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

use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\Exception\LogicException;

/**
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 */
class IntlFormatter implements IntlFormatterInterface
{
    private bool $hasMessageFormatter;
    private array $cache = [];

    public function formatIntl(string $message, string $locale, array $parameters = []): string
    {
        // MessageFormatter constructor throws an exception if the message is empty
        if ('' === $message) {
            return '';
        }

        if (!$formatter = $this->cache[$locale][$message] ?? null) {
            if (!$this->hasMessageFormatter ??= class_exists(\MessageFormatter::class)) {
                throw new LogicException('Cannot parse message translation: please install the "intl" PHP extension or the "symfony/polyfill-intl-messageformatter" package.');
            }
            try {
                $this->cache[$locale][$message] = $formatter = new \MessageFormatter($locale, $message);
            } catch (\IntlException $e) {
                throw new InvalidArgumentException(\sprintf('Invalid message format (error #%d): ', intl_get_error_code()).intl_get_error_message(), 0, $e);
            }
        }

        foreach ($parameters as $key => $value) {
            if (\in_array($key[0] ?? null, ['%', '{'], true)) {
                unset($parameters[$key]);
                $parameters[trim($key, '%{ }')] = $value;
            }
        }

        if (false === $message = $formatter->format($parameters)) {
            throw new InvalidArgumentException(\sprintf('Unable to format message (error #%s): ', $formatter->getErrorCode()).$formatter->getErrorMessage());
        }

        return $message;
    }
}
