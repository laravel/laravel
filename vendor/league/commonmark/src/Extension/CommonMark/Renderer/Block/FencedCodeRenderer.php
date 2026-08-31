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

use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\Xml;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class FencedCodeRenderer implements NodeRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param FencedCode $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
    {
        FencedCode::assertInstanceOf($node);

        $attrs = $node->data->getData('attributes');

        $infoWords = $node->getInfoWords();
        if (\count($infoWords) !== 0 && $infoWords[0] !== '') {
            $class = $infoWords[0];
            if (! \str_starts_with($class, 'language-')) {
                $class = 'language-' . $class;
            }

            $attrs->append('class', $class);
        }

        return new HtmlElement(
            'pre',
            [],
            new HtmlElement('code', $attrs->export(), Xml::escape($node->getLiteral()))
        );
    }

    public function getXmlTagName(Node $node): string
    {
        return 'code_block';
    }

    /**
     * @param FencedCode $node
     *
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        FencedCode::assertInstanceOf($node);

        if (($info = $node->getInfo()) === null || $info === '') {
            return [];
        }

        return ['info' => $info];
    }
}
