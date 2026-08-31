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

namespace League\CommonMark\Extension\FrontMatter\Input;

use League\CommonMark\Extension\FrontMatter\FrontMatterProviderInterface;
use League\CommonMark\Input\MarkdownInput;

final class MarkdownInputWithFrontMatter extends MarkdownInput implements FrontMatterProviderInterface
{
    /** @var mixed|null */
    private $frontMatter;

    /**
     * @param string     $content     Markdown content without the raw front matter
     * @param int        $lineOffset  Line offset (based on number of front matter lines removed)
     * @param mixed|null $frontMatter Parsed front matter
     */
    public function __construct(string $content, int $lineOffset = 0, $frontMatter = null)
    {
        parent::__construct($content, $lineOffset);

        $this->frontMatter = $frontMatter;
    }

    /**
     * {@inheritDoc}
     */
    public function getFrontMatter()
    {
        return $this->frontMatter;
    }
}
