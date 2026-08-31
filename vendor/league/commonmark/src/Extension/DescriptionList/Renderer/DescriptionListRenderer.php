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

namespace League\CommonMark\Extension\DescriptionList\Renderer;

use League\CommonMark\Extension\DescriptionList\Node\DescriptionList;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

final class DescriptionListRenderer implements NodeRendererInterface
{
    /**
     * @param DescriptionList $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        DescriptionList::assertInstanceOf($node);

        $separator = $childRenderer->getBlockSeparator();

        return new HtmlElement('dl', [], $separator . $childRenderer->renderNodes($node->children()) . $separator);
    }
}
