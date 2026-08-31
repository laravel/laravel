<?php

declare(strict_types=1);

/*
 * This is part of the league/commonmark package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Table;

use League\CommonMark\Node\Block\AbstractBlock;

final class TableCell extends AbstractBlock
{
    public const TYPE_HEADER = 'header';
    public const TYPE_DATA   = 'data';

    public const ALIGN_LEFT   = 'left';
    public const ALIGN_RIGHT  = 'right';
    public const ALIGN_CENTER = 'center';

    /**
     * @psalm-var self::TYPE_*
     * @phpstan-var self::TYPE_*
     *
     * @psalm-readonly-allow-private-mutation
     */
    private string $type = self::TYPE_DATA;

    /**
     * @psalm-var self::ALIGN_*|null
     * @phpstan-var self::ALIGN_*|null
     *
     * @psalm-readonly-allow-private-mutation
     */
    private ?string $align = null;

    /**
     * @psalm-param self::TYPE_* $type
     * @psalm-param self::ALIGN_*|null $align
     *
     * @phpstan-param self::TYPE_* $type
     * @phpstan-param self::ALIGN_*|null $align
     */
    public function __construct(string $type = self::TYPE_DATA, ?string $align = null)
    {
        parent::__construct();

        $this->type  = $type;
        $this->align = $align;
    }

    /**
     * @psalm-return self::TYPE_*
     *
     * @phpstan-return self::TYPE_*
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @psalm-param self::TYPE_* $type
     *
     * @phpstan-param self::TYPE_* $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @psalm-return self::ALIGN_*|null
     *
     * @phpstan-return self::ALIGN_*|null
     */
    public function getAlign(): ?string
    {
        return $this->align;
    }

    /**
     * @psalm-param self::ALIGN_*|null $align
     *
     * @phpstan-param self::ALIGN_*|null $align
     */
    public function setAlign(?string $align): void
    {
        $this->align = $align;
    }
}
