<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\HtmlToTextConverter;

use League\HTMLToMarkdown\HtmlConverter;
use League\HTMLToMarkdown\HtmlConverterInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class LeagueHtmlToMarkdownConverter implements HtmlToTextConverterInterface
{
    public function __construct(
        private HtmlConverterInterface $converter = new HtmlConverter([
            'hard_break' => true,
            'strip_tags' => true,
            'remove_nodes' => 'head style',
        ]),
    ) {
    }

    public function convert(string $html, string $charset): string
    {
        return $this->converter->convert($html);
    }
}
