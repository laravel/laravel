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

namespace League\CommonMark\Extension\FrontMatter;

use League\CommonMark\Extension\FrontMatter\Input\MarkdownInputWithFrontMatter;

interface FrontMatterParserInterface
{
    public function parse(string $markdownContent): MarkdownInputWithFrontMatter;
}
