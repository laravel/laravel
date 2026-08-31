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

namespace League\CommonMark\Event;

use League\CommonMark\Node\Block\Document;

/**
 * Event dispatched when the document has been fully parsed
 */
final class DocumentParsedEvent extends AbstractEvent
{
    /** @psalm-readonly */
    private Document $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }
}
