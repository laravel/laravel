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

namespace League\CommonMark\Extension\Attributes\Node;

use League\CommonMark\Node\Block\AbstractBlock;

final class Attributes extends AbstractBlock
{
    public const TARGET_PARENT   = 0;
    public const TARGET_PREVIOUS = 1;
    public const TARGET_NEXT     = 2;

    /** @var array<string, mixed> */
    private array $attributes;

    private int $target = self::TARGET_NEXT;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes)
    {
        parent::__construct();

        $this->attributes = $attributes;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getTarget(): int
    {
        return $this->target;
    }

    public function setTarget(int $target): void
    {
        $this->target = $target;
    }
}
