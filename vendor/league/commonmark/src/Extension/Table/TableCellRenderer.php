<?php

declare(strict_types=1);

/*
 * This is part of the league/commonmark package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Table;

use League\CommonMark\Extension\Attributes\Util\AttributesHelper;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class TableCellRenderer implements NodeRendererInterface, XmlNodeRendererInterface
{
    private const DEFAULT_ATTRIBUTES = [
        TableCell::ALIGN_LEFT   => ['align' => 'left'],
        TableCell::ALIGN_CENTER => ['align' => 'center'],
        TableCell::ALIGN_RIGHT  => ['align' => 'right'],
    ];

    /** @var array<TableCell::ALIGN_*, array<string, string|string[]|bool>> */
    private array $alignmentAttributes;

    /**
     * @param array<TableCell::ALIGN_*, array<string, string|string[]|bool>> $alignmentAttributes
     */
    public function __construct(array $alignmentAttributes = self::DEFAULT_ATTRIBUTES)
    {
        $this->alignmentAttributes = $alignmentAttributes;
    }

    /**
     * @param TableCell $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
    {
        TableCell::assertInstanceOf($node);

        $attrs = $node->data->get('attributes');
        if (($alignment = $node->getAlign()) !== null) {
            $attrs = AttributesHelper::mergeAttributes($attrs, $this->alignmentAttributes[$alignment]);
        }

        $tag = $node->getType() === TableCell::TYPE_HEADER ? 'th' : 'td';

        return new HtmlElement($tag, $attrs, $childRenderer->renderNodes($node->children()));
    }

    public function getXmlTagName(Node $node): string
    {
        return 'table_cell';
    }

    /**
     * @param TableCell $node
     *
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        TableCell::assertInstanceOf($node);

        $ret = ['type' => $node->getType()];

        if (($align = $node->getAlign()) !== null) {
            $ret['align'] = $align;
        }

        return $ret;
    }
}
