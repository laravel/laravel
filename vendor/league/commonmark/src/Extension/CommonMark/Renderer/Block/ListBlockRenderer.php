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

namespace League\CommonMark\Extension\CommonMark\Renderer\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class ListBlockRenderer implements NodeRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param ListBlock $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
    {
        ListBlock::assertInstanceOf($node);

        $listData = $node->getListData();

        $tag = $listData->type === ListBlock::TYPE_BULLET ? 'ul' : 'ol';

        $attrs = $node->data->get('attributes');

        if ($listData->start !== null && $listData->start !== 1) {
            $attrs['start'] = (string) $listData->start;
        }

        $innerSeparator = $childRenderer->getInnerSeparator();

        return new HtmlElement($tag, $attrs, $innerSeparator . $childRenderer->renderNodes($node->children()) . $innerSeparator);
    }

    public function getXmlTagName(Node $node): string
    {
        return 'list';
    }

    /**
     * @param ListBlock $node
     *
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        ListBlock::assertInstanceOf($node);

        $data = $node->getListData();

        if ($data->type === ListBlock::TYPE_BULLET) {
            return [
                'type' => $data->type,
                'tight' => $node->isTight() ? 'true' : 'false',
            ];
        }

        return [
            'type' => $data->type,
            'start' => $data->start ?? 1,
            'tight' => $node->isTight(),
            'delimiter' => $data->delimiter ?? ListBlock::DELIM_PERIOD,
        ];
    }
}
