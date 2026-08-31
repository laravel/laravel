<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Node;

/**
 * Interface for a node which directly contains line(s) of text
 */
interface StringContainerInterface
{
    public function setLiteral(string $literal): void;

    public function getLiteral(): string;
}
