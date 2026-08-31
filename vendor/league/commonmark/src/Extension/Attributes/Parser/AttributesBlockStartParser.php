<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) 2015 Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Attributes\Parser;

use League\CommonMark\Extension\Attributes\Util\AttributesHelper;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

final class AttributesBlockStartParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        $originalPosition = $cursor->getPosition();
        $attributes       = AttributesHelper::parseAttributes($cursor);

        if ($attributes === [] && $originalPosition === $cursor->getPosition()) {
            return BlockStart::none();
        }

        if ($cursor->getNextNonSpaceCharacter() !== null) {
            return BlockStart::none();
        }

        return BlockStart::of(new AttributesBlockContinueParser($attributes, $parserState->getActiveBlockParser()->getBlock()))->at($cursor);
    }
}
