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

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContentsPlaceholder;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\NodeIterator;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;
use League\Config\Exception\InvalidConfigurationException;

final class TableOfContentsBuilder implements ConfigurationAwareInterface
{
    public const POSITION_TOP             = 'top';
    public const POSITION_BEFORE_HEADINGS = 'before-headings';
    public const POSITION_PLACEHOLDER     = 'placeholder';

    /** @psalm-readonly-allow-private-mutation */
    private ConfigurationInterface $config;

    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        $document = $event->getDocument();

        $generator = new TableOfContentsGenerator(
            (string) $this->config->get('table_of_contents/style'),
            (string) $this->config->get('table_of_contents/normalize'),
            (int) $this->config->get('table_of_contents/min_heading_level'),
            (int) $this->config->get('table_of_contents/max_heading_level'),
            (string) $this->config->get('heading_permalink/fragment_prefix'),
        );

        $toc = $generator->generate($document);
        if ($toc === null) {
            // No linkable headers exist, so no TOC could be generated
            return;
        }

        // Add custom CSS class(es), if defined
        $class = $this->config->get('table_of_contents/html_class');
        if ($class !== null) {
            $toc->data->append('attributes/class', $class);
        }

        // Add the TOC to the Document
        $position = $this->config->get('table_of_contents/position');
        if ($position === self::POSITION_TOP) {
            $document->prependChild($toc);
        } elseif ($position === self::POSITION_BEFORE_HEADINGS) {
            $this->insertBeforeFirstLinkedHeading($document, $toc);
        } elseif ($position === self::POSITION_PLACEHOLDER) {
            $this->replacePlaceholders($document, $toc);
        } else {
            throw InvalidConfigurationException::forConfigOption('table_of_contents/position', $position);
        }
    }

    private function insertBeforeFirstLinkedHeading(Document $document, TableOfContents $toc): void
    {
        foreach ($document->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if (! $node instanceof Heading) {
                continue;
            }

            foreach ($node->children() as $child) {
                if ($child instanceof HeadingPermalink) {
                    $node->insertBefore($toc);

                    return;
                }
            }
        }
    }

    private function replacePlaceholders(Document $document, TableOfContents $toc): void
    {
        foreach ($document->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            // Add the block once we find a placeholder
            if (! $node instanceof TableOfContentsPlaceholder) {
                continue;
            }

            $node->replaceWith(clone $toc);
        }
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }
}
