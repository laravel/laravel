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

namespace League\CommonMark\Extension\TableOfContents;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class TableOfContentsPlaceholderRenderer implements NodeRendererInterface, XmlNodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        return '<!-- table of contents -->';
    }

    public function getXmlTagName(Node $node): string
    {
        return 'table_of_contents_placeholder';
    }

    /**
     * @return array<string, scalar>
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}
