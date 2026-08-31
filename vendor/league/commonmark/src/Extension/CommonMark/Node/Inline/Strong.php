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

namespace League\CommonMark\Extension\CommonMark\Node\Inline;

use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Inline\DelimitedInterface;

final class Strong extends AbstractInline implements DelimitedInterface
{
    private string $delimiter;

    public function __construct(string $delimiter = '**')
    {
        parent::__construct();

        $this->delimiter = $delimiter;
    }

    public function getOpeningDelimiter(): string
    {
        return $this->delimiter;
    }

    public function getClosingDelimiter(): string
    {
        return $this->delimiter;
    }
}
