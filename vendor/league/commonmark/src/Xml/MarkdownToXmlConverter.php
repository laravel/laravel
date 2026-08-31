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

namespace League\CommonMark\Xml;

use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Parser\MarkdownParserInterface;
use League\CommonMark\Renderer\DocumentRendererInterface;

final class MarkdownToXmlConverter implements ConverterInterface
{
    /** @psalm-readonly */
    private MarkdownParserInterface $parser;

    /** @psalm-readonly */
    private DocumentRendererInterface $renderer;

    public function __construct(EnvironmentInterface $environment)
    {
        $this->parser   = new MarkdownParser($environment);
        $this->renderer = new XmlRenderer($environment);
    }

    /**
     * Converts Markdown to XML
     *
     * @throws CommonMarkException
     */
    public function convert(string $input): RenderedContentInterface
    {
        return $this->renderer->renderDocument($this->parser->parse($input));
    }

    /**
     * Converts CommonMark to HTML.
     *
     * @see MarkdownToXmlConverter::convert()
     *
     * @throws CommonMarkException
     */
    public function __invoke(string $input): RenderedContentInterface
    {
        return $this->convert($input);
    }
}
