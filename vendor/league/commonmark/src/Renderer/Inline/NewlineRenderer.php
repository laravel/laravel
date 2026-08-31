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

namespace League\CommonMark\Renderer\Inline;

use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Xml\XmlNodeRendererInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

final class NewlineRenderer implements NodeRendererInterface, XmlNodeRendererInterface, ConfigurationAwareInterface
{
    /** @psalm-readonly-allow-private-mutation */
    private ConfigurationInterface $config;

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    /**
     * @param Newline $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        Newline::assertInstanceOf($node);

        if ($node->getType() === Newline::HARDBREAK) {
            return "<br />\n";
        }

        return $this->config->get('renderer/soft_break');
    }

    /**
     * @param Newline $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlTagName(Node $node): string
    {
        Newline::assertInstanceOf($node);

        return $node->getType() === Newline::SOFTBREAK ? 'softbreak' : 'linebreak';
    }

    /**
     * {@inheritDoc}
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}
