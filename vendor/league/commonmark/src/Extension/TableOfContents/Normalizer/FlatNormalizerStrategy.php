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

namespace League\CommonMark\Extension\TableOfContents\Normalizer;

use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;

final class FlatNormalizerStrategy implements NormalizerStrategyInterface
{
    /** @psalm-readonly */
    private TableOfContents $toc;

    public function __construct(TableOfContents $toc)
    {
        $this->toc = $toc;
    }

    public function addItem(int $level, ListItem $listItemToAdd): void
    {
        $this->toc->appendChild($listItemToAdd);
    }
}
