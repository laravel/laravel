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

namespace League\CommonMark\Reference;

/**
 * @psalm-immutable
 */
final class Reference implements ReferenceInterface
{
    /** @psalm-readonly */
    private string $label;

    /** @psalm-readonly */
    private string $destination;

    /** @psalm-readonly */
    private string $title;

    public function __construct(string $label, string $destination, string $title)
    {
        $this->label       = $label;
        $this->destination = $destination;
        $this->title       = $title;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
