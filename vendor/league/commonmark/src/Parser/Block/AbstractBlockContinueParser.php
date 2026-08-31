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

namespace League\CommonMark\Parser\Block;

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * Base class for a block parser
 *
 * Slightly more convenient to extend from vs. implementing the interface
 */
abstract class AbstractBlockContinueParser implements BlockContinueParserInterface
{
    public function isContainer(): bool
    {
        return false;
    }

    public function canHaveLazyContinuationLines(): bool
    {
        return false;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return false;
    }

    public function addLine(string $line): void
    {
    }

    public function closeBlock(): void
    {
    }
}
