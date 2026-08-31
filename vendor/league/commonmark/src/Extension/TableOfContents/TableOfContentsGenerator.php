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

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\Normalizer\AsIsNormalizerStrategy;
use League\CommonMark\Extension\TableOfContents\Normalizer\FlatNormalizerStrategy;
use League\CommonMark\Extension\TableOfContents\Normalizer\NormalizerStrategyInterface;
use League\CommonMark\Extension\TableOfContents\Normalizer\RelativeNormalizerStrategy;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\NodeIterator;
use League\CommonMark\Node\RawMarkupContainerInterface;
use League\CommonMark\Node\StringContainerHelper;
use League\Config\Exception\InvalidConfigurationException;

final class TableOfContentsGenerator implements TableOfContentsGeneratorInterface
{
    public const STYLE_BULLET  = ListBlock::TYPE_BULLET;
    public const STYLE_ORDERED = ListBlock::TYPE_ORDERED;

    public const NORMALIZE_DISABLED = 'as-is';
    public const NORMALIZE_RELATIVE = 'relative';
    public const NORMALIZE_FLAT     = 'flat';

    /** @psalm-readonly */
    private string $style;

    /** @psalm-readonly */
    private string $normalizationStrategy;

    /** @psalm-readonly */
    private int $minHeadingLevel;

    /** @psalm-readonly */
    private int $maxHeadingLevel;

    /** @psalm-readonly */
    private string $fragmentPrefix;

    public function __construct(string $style, string $normalizationStrategy, int $minHeadingLevel, int $maxHeadingLevel, string $fragmentPrefix)
    {
        $this->style                 = $style;
        $this->normalizationStrategy = $normalizationStrategy;
        $this->minHeadingLevel       = $minHeadingLevel;
        $this->maxHeadingLevel       = $maxHeadingLevel;
        $this->fragmentPrefix        = $fragmentPrefix === '' ? '' : $fragmentPrefix . '-';
    }

    public function generate(Document $document): ?TableOfContents
    {
        $toc = $this->createToc($document);

        $normalizer = $this->getNormalizer($toc);

        $firstHeading = null;

        foreach ($this->getHeadingLinks($document) as $headingLink) {
            $heading = $headingLink->parent();
            // Make sure this is actually tied to a heading
            if (! $heading instanceof Heading) {
                continue;
            }

            // Skip any headings outside the configured min/max levels
            if ($heading->getLevel() < $this->minHeadingLevel || $heading->getLevel() > $this->maxHeadingLevel) {
                continue;
            }

            // Keep track of the first heading we see - we might need this later
            $firstHeading ??= $heading;

            // Keep track of the start and end lines
            $toc->setStartLine($firstHeading->getStartLine());
            $toc->setEndLine($heading->getEndLine());

            // Create the new link
            $link = new Link('#' . $this->fragmentPrefix . $headingLink->getSlug(), StringContainerHelper::getChildText($heading, [RawMarkupContainerInterface::class]));

            $listItem = new ListItem($toc->getListData());
            $listItem->setStartLine($heading->getStartLine());
            $listItem->setEndLine($heading->getEndLine());
            $listItem->appendChild($link);

            // Add it to the correct place
            $normalizer->addItem($heading->getLevel(), $listItem);
        }

        // Don't add the TOC if no headings were present
        if (! $toc->hasChildren() || $firstHeading === null) {
            return null;
        }

        return $toc;
    }

    private function createToc(Document $document): TableOfContents
    {
        $listData = new ListData();

        if ($this->style === self::STYLE_BULLET) {
            $listData->type = ListBlock::TYPE_BULLET;
        } elseif ($this->style === self::STYLE_ORDERED) {
            $listData->type = ListBlock::TYPE_ORDERED;
        } else {
            throw new InvalidConfigurationException(\sprintf('Invalid table of contents list style: "%s"', $this->style));
        }

        $toc = new TableOfContents($listData);

        $toc->setStartLine($document->getStartLine());
        $toc->setEndLine($document->getEndLine());

        return $toc;
    }

    /**
     * @return iterable<HeadingPermalink>
     */
    private function getHeadingLinks(Document $document): iterable
    {
        foreach ($document->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if (! $node instanceof Heading) {
                continue;
            }

            foreach ($node->children() as $child) {
                if ($child instanceof HeadingPermalink) {
                    yield $child;
                }
            }
        }
    }

    private function getNormalizer(TableOfContents $toc): NormalizerStrategyInterface
    {
        switch ($this->normalizationStrategy) {
            case self::NORMALIZE_DISABLED:
                return new AsIsNormalizerStrategy($toc);
            case self::NORMALIZE_RELATIVE:
                return new RelativeNormalizerStrategy($toc);
            case self::NORMALIZE_FLAT:
                return new FlatNormalizerStrategy($toc);
            default:
                throw new InvalidConfigurationException(\sprintf('Invalid table of contents normalization strategy: "%s"', $this->normalizationStrategy));
        }
    }
}
