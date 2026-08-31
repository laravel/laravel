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

namespace League\CommonMark\Node\Inline;

final class Newline extends AbstractInline
{
    // Any changes to these constants should be reflected in .phpstorm.meta.php
    public const HARDBREAK = 0;
    public const SOFTBREAK = 1;

    /** @psalm-readonly */
    private int $type;

    public function __construct(int $breakType = self::HARDBREAK)
    {
        parent::__construct();

        $this->type = $breakType;
    }

    /** @psalm-immutable */
    public function getType(): int
    {
        return $this->type;
    }
}
