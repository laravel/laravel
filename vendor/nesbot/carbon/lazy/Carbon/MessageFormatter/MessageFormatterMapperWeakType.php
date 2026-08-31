<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon\MessageFormatter;

use Symfony\Component\Translation\Formatter\ChoiceMessageFormatterInterface;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;

if (!class_exists(LazyMessageFormatter::class, false)) {
    abstract class LazyMessageFormatter implements MessageFormatterInterface, ChoiceMessageFormatterInterface
    {
        abstract protected function transformLocale(?string $locale): ?string;

        public function format($message, $locale, array $parameters = [])
        {
            return $this->formatter->format(
                $message,
                $this->transformLocale($locale),
                $parameters
            );
        }

        public function choiceFormat($message, $number, $locale, array $parameters = [])
        {
            return $this->formatter->choiceFormat($message, $number, $locale, $parameters);
        }
    }
}
