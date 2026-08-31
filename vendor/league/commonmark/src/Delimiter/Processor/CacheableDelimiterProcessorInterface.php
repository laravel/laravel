<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Delimiter\Processor;

use League\CommonMark\Delimiter\DelimiterInterface;

/**
 * Special marker interface for delimiter processors that return dynamic values from getDelimiterUse()
 *
 * In order to guarantee linear performance of delimiter processing, the delimiter stack must be able to
 * cache the lower bound when searching for a matching opener. This gets complicated for delimiter processors
 * that use a dynamic number of characters (like with emphasis and its "multiple of 3" rule).
 */
interface CacheableDelimiterProcessorInterface extends DelimiterProcessorInterface
{
    /**
     * Returns a cache key of the factors that determine the number of characters to use.
     *
     * In order to guarantee linear performance of delimiter processing, the delimiter stack must be able to
     * cache the lower bound when searching for a matching opener. This lower bound is usually quite simple;
     * for example, with quotes, it's just the last opener with that characted. However, this gets complicated
     * for delimiter processors that use a dynamic number of characters (like with emphasis and its "multiple
     * of 3" rule), because the delimiter length being considered may change during processing because of that
     * dynamic logic in getDelimiterUse(). Therefore, we cannot safely cache the lower bound for these dynamic
     * processors without knowing the factors that determine the number of characters to use.
     *
     * At a minimum, this should include the delimiter character, plus any other factors used to determine
     * the result of getDelimiterUse(). The format of the string is not important so long as it is unique
     * (compared to other processors) and consistent for a given set of factors.
     *
     * If getDelimiterUse() always returns the same hard-coded value, this method should return just
     * the delimiter character.
     */
    public function getCacheKey(DelimiterInterface $closer): string;
}
