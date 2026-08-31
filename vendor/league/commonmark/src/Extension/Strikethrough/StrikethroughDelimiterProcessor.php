<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com> and uAfrica.com (http://uafrica.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Strikethrough;

use League\CommonMark\Delimiter\DelimiterInterface;
use League\CommonMark\Delimiter\Processor\CacheableDelimiterProcessorInterface;
use League\CommonMark\Node\Inline\AbstractStringContainer;

final class StrikethroughDelimiterProcessor implements CacheableDelimiterProcessorInterface
{
    public function getOpeningCharacter(): string
    {
        return '~';
    }

    public function getClosingCharacter(): string
    {
        return '~';
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getDelimiterUse(DelimiterInterface $opener, DelimiterInterface $closer): int
    {
        if ($opener->getLength() > 2 && $closer->getLength() > 2) {
            return 0;
        }

        if ($opener->getLength() !== $closer->getLength()) {
            return 0;
        }

        // $opener and $closer are the same length so we just return one of them
        return $opener->getLength();
    }

    public function process(AbstractStringContainer $opener, AbstractStringContainer $closer, int $delimiterUse): void
    {
        $strikethrough = new Strikethrough(\str_repeat('~', $delimiterUse));

        $tmp = $opener->next();
        while ($tmp !== null && $tmp !== $closer) {
            $next = $tmp->next();
            $strikethrough->appendChild($tmp);
            $tmp = $next;
        }

        $opener->insertAfter($strikethrough);
    }

    public function getCacheKey(DelimiterInterface $closer): string
    {
        return '~' . $closer->getLength();
    }
}
