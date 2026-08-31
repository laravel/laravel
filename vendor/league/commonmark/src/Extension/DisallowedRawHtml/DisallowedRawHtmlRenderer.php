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

namespace League\CommonMark\Extension\DisallowedRawHtml;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

final class DisallowedRawHtmlRenderer implements NodeRendererInterface, ConfigurationAwareInterface
{
    /** @psalm-readonly */
    private NodeRendererInterface $innerRenderer;

    /** @psalm-readonly-allow-private-mutation */
    private ConfigurationInterface $config;

    public function __construct(NodeRendererInterface $innerRenderer)
    {
        $this->innerRenderer = $innerRenderer;
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): ?string
    {
        $rendered = (string) $this->innerRenderer->render($node, $childRenderer);

        if ($rendered === '') {
            return '';
        }

        $tags = (array) $this->config->get('disallowed_raw_html/disallowed_tags');
        if (\count($tags) === 0) {
            return $rendered;
        }

        $regex = \sprintf('/<(\/?(?:%s)[\s\/>])/i', \implode('|', \array_map('preg_quote', $tags)));

        // Match these types of tags: <title> </title> <title x="sdf"> <title/> <title />
        return \preg_replace($regex, '&lt;$1', $rendered);
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;

        if ($this->innerRenderer instanceof ConfigurationAwareInterface) {
            $this->innerRenderer->setConfiguration($configuration);
        }
    }
}
