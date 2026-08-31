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

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Renderer\Block\ListBlockRenderer;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContentsPlaceholder;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

final class TableOfContentsExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('table_of_contents', Expect::structure([
            'position' => Expect::anyOf(TableOfContentsBuilder::POSITION_BEFORE_HEADINGS, TableOfContentsBuilder::POSITION_PLACEHOLDER, TableOfContentsBuilder::POSITION_TOP)->default(TableOfContentsBuilder::POSITION_TOP),
            'style' => Expect::anyOf(ListBlock::TYPE_BULLET, ListBlock::TYPE_ORDERED)->default(ListBlock::TYPE_BULLET),
            'normalize' => Expect::anyOf(TableOfContentsGenerator::NORMALIZE_RELATIVE, TableOfContentsGenerator::NORMALIZE_FLAT, TableOfContentsGenerator::NORMALIZE_DISABLED)->default(TableOfContentsGenerator::NORMALIZE_RELATIVE),
            'min_heading_level' => Expect::int()->min(1)->max(6)->default(1),
            'max_heading_level' => Expect::int()->min(1)->max(6)->default(6),
            'html_class' => Expect::string()->default('table-of-contents'),
            'placeholder' => Expect::anyOf(Expect::string(), Expect::null())->default(null),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addRenderer(TableOfContents::class, new TableOfContentsRenderer(new ListBlockRenderer()));
        $environment->addEventListener(DocumentParsedEvent::class, [new TableOfContentsBuilder(), 'onDocumentParsed'], -150);

        // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
        if ($environment->getConfiguration()->get('table_of_contents/position') === TableOfContentsBuilder::POSITION_PLACEHOLDER) {
            $environment->addBlockStartParser(TableOfContentsPlaceholderParser::blockStartParser(), 200);
            // If a placeholder cannot be replaced with a TOC element this renderer will ensure the parser won't error out
            $environment->addRenderer(TableOfContentsPlaceholder::class, new TableOfContentsPlaceholderRenderer());
        }
    }
}
