<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) Rezo Zero / Ambroise Maupate
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Footnote\Renderer;

use League\CommonMark\Extension\Footnote\Node\FootnoteContainer;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Xml\XmlNodeRendererInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

final class FootnoteContainerRenderer implements NodeRendererInterface, XmlNodeRendererInterface, ConfigurationAwareInterface
{
    private ConfigurationInterface $config;

    /**
     * @param FootnoteContainer $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
    {
        FootnoteContainer::assertInstanceOf($node);

        $attrs = $node->data->getData('attributes');

        $attrs->append('class', $this->config->get('footnote/container_class'));
        $attrs->set('role', 'doc-endnotes');

        $contents = new HtmlElement('ol', [], $childRenderer->renderNodes($node->children()));
        if ($this->config->get('footnote/container_add_hr')) {
            $contents = [new HtmlElement('hr', [], null, true), $contents];
        }

        return new HtmlElement('div', $attrs->export(), $contents);
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function getXmlTagName(Node $node): string
    {
        return 'footnote_container';
    }

    /**
     * @return array<string, scalar>
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}
