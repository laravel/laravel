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

namespace League\CommonMark\Output;

use League\CommonMark\Node\Block\Document;

class RenderedContent implements RenderedContentInterface, \Stringable
{
    /** @psalm-readonly */
    private Document $document;

    /** @psalm-readonly */
    private string $content;

    public function __construct(Document $document, string $content)
    {
        $this->document = $document;
        $this->content  = $content;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @psalm-mutation-free
     */
    public function __toString(): string
    {
        return $this->content;
    }
}
