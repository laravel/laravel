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

namespace League\CommonMark\Extension\FrontMatter\Listener;

use League\CommonMark\Event\DocumentPreParsedEvent;
use League\CommonMark\Extension\FrontMatter\FrontMatterParserInterface;

final class FrontMatterPreParser
{
    private FrontMatterParserInterface $parser;

    public function __construct(FrontMatterParserInterface $parser)
    {
        $this->parser = $parser;
    }

    public function __invoke(DocumentPreParsedEvent $event): void
    {
        $content = $event->getMarkdown()->getContent();

        $parsed = $this->parser->parse($content);

        $event->getDocument()->data->set('front_matter', $parsed->getFrontMatter());
        $event->replaceMarkdown($parsed);
    }
}
