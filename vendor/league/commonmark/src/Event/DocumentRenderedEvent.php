<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Event;

use League\CommonMark\Output\RenderedContentInterface;

final class DocumentRenderedEvent extends AbstractEvent
{
    private RenderedContentInterface $output;

    public function __construct(RenderedContentInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @psalm-mutation-free
     */
    public function getOutput(): RenderedContentInterface
    {
        return $this->output;
    }

    /**
     * @psalm-external-mutation-free
     */
    public function replaceOutput(RenderedContentInterface $output): void
    {
        $this->output = $output;
    }
}
