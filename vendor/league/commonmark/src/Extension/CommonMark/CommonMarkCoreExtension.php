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

namespace League\CommonMark\Extension\CommonMark;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Delimiter\Processor\EmphasisDelimiterProcessor;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Node as CoreNode;
use League\CommonMark\Parser as CoreParser;
use League\CommonMark\Renderer as CoreRenderer;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

final class CommonMarkCoreExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('commonmark', Expect::structure([
            'use_asterisk' => Expect::bool(true),
            'use_underscore' => Expect::bool(true),
            'enable_strong' => Expect::bool(true),
            'enable_em' => Expect::bool(true),
            'unordered_list_markers' => Expect::listOf('string')->min(1)->default(['*', '+', '-'])->mergeDefaults(false),
        ]));
    }

    // phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma,Squiz.WhiteSpace.SemicolonSpacing.Incorrect
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addBlockStartParser(new Parser\Block\BlockQuoteStartParser(),     70)
            ->addBlockStartParser(new Parser\Block\HeadingStartParser(),        60)
            ->addBlockStartParser(new Parser\Block\FencedCodeStartParser(),     50)
            ->addBlockStartParser(new Parser\Block\HtmlBlockStartParser(),      40)
            ->addBlockStartParser(new Parser\Block\ThematicBreakStartParser(),  20)
            ->addBlockStartParser(new Parser\Block\ListBlockStartParser(),      10)
            ->addBlockStartParser(new Parser\Block\IndentedCodeStartParser(), -100)

            ->addInlineParser(new CoreParser\Inline\NewlineParser(), 200)
            ->addInlineParser(new Parser\Inline\BacktickParser(),    150)
            ->addInlineParser(new Parser\Inline\EscapableParser(),    80)
            ->addInlineParser(new Parser\Inline\EntityParser(),       70)
            ->addInlineParser(new Parser\Inline\AutolinkParser(),     50)
            ->addInlineParser(new Parser\Inline\HtmlInlineParser(),   40)
            ->addInlineParser(new Parser\Inline\CloseBracketParser(), 30)
            ->addInlineParser(new Parser\Inline\OpenBracketParser(),  20)
            ->addInlineParser(new Parser\Inline\BangParser(),         10)

            ->addRenderer(Node\Block\BlockQuote::class,    new Renderer\Block\BlockQuoteRenderer(),    0)
            ->addRenderer(CoreNode\Block\Document::class,  new CoreRenderer\Block\DocumentRenderer(),  0)
            ->addRenderer(Node\Block\FencedCode::class,    new Renderer\Block\FencedCodeRenderer(),    0)
            ->addRenderer(Node\Block\Heading::class,       new Renderer\Block\HeadingRenderer(),       0)
            ->addRenderer(Node\Block\HtmlBlock::class,     new Renderer\Block\HtmlBlockRenderer(),     0)
            ->addRenderer(Node\Block\IndentedCode::class,  new Renderer\Block\IndentedCodeRenderer(),  0)
            ->addRenderer(Node\Block\ListBlock::class,     new Renderer\Block\ListBlockRenderer(),     0)
            ->addRenderer(Node\Block\ListItem::class,      new Renderer\Block\ListItemRenderer(),      0)
            ->addRenderer(CoreNode\Block\Paragraph::class, new CoreRenderer\Block\ParagraphRenderer(), 0)
            ->addRenderer(Node\Block\ThematicBreak::class, new Renderer\Block\ThematicBreakRenderer(), 0)

            ->addRenderer(Node\Inline\Code::class,        new Renderer\Inline\CodeRenderer(),        0)
            ->addRenderer(Node\Inline\Emphasis::class,    new Renderer\Inline\EmphasisRenderer(),    0)
            ->addRenderer(Node\Inline\HtmlInline::class,  new Renderer\Inline\HtmlInlineRenderer(),  0)
            ->addRenderer(Node\Inline\Image::class,       new Renderer\Inline\ImageRenderer(),       0)
            ->addRenderer(Node\Inline\Link::class,        new Renderer\Inline\LinkRenderer(),        0)
            ->addRenderer(CoreNode\Inline\Newline::class, new CoreRenderer\Inline\NewlineRenderer(), 0)
            ->addRenderer(Node\Inline\Strong::class,      new Renderer\Inline\StrongRenderer(),      0)
            ->addRenderer(CoreNode\Inline\Text::class,    new CoreRenderer\Inline\TextRenderer(),    0)
        ;

        if ($environment->getConfiguration()->get('commonmark/use_asterisk')) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('*'));
        }

        if ($environment->getConfiguration()->get('commonmark/use_underscore')) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('_'));
        }
    }
}
