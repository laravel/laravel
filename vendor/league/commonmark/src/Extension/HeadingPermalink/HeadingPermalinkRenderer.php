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

namespace League\CommonMark\Extension\HeadingPermalink;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Xml\XmlNodeRendererInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

/**
 * Renders the HeadingPermalink elements
 */
final class HeadingPermalinkRenderer implements NodeRendererInterface, XmlNodeRendererInterface, ConfigurationAwareInterface
{
    public const DEFAULT_SYMBOL = '¶';

    /** @psalm-readonly-allow-private-mutation */
    private ConfigurationInterface $config;

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    /**
     * @param HeadingPermalink $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
    {
        HeadingPermalink::assertInstanceOf($node);

        $slug = $node->getSlug();

        $fragmentPrefix = (string) $this->config->get('heading_permalink/fragment_prefix');
        if ($fragmentPrefix !== '') {
            $fragmentPrefix .= '-';
        }

        $attrs    = $node->data->getData('attributes');
        $appendId = ! $this->config->get('heading_permalink/apply_id_to_heading');

        if ($appendId) {
            $idPrefix = (string) $this->config->get('heading_permalink/id_prefix');

            if ($idPrefix !== '') {
                $idPrefix .= '-';
            }

            $attrs->set('id', $idPrefix . $slug);
        }

        $attrs->set('href', '#' . $fragmentPrefix . $slug);
        $attrs->append('class', $this->config->get('heading_permalink/html_class'));

        $hidden = $this->config->get('heading_permalink/aria_hidden');
        if ($hidden) {
            $attrs->set('aria-hidden', 'true');
        }

        $attrs->set('title', $this->config->get('heading_permalink/title'));

        $symbol = $this->config->get('heading_permalink/symbol');
        \assert(\is_string($symbol));

        return new HtmlElement('a', $attrs->export(), \htmlspecialchars($symbol), false);
    }

    public function getXmlTagName(Node $node): string
    {
        return 'heading_permalink';
    }

    /**
     * @param HeadingPermalink $node
     *
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        HeadingPermalink::assertInstanceOf($node);

        return [
            'slug' => $node->getSlug(),
        ];
    }
}
