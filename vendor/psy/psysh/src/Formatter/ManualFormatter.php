<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Formatter;

use Psy\Manual\ManualInterface;
use Psy\Output\Theme;
use Psy\Readline\Interactive\Layout\DisplayString;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\Helper;

/**
 * Formats structured manual data for display at runtime.
 *
 * Takes structured data from the v3 manual format and formats it for display,
 * adapting to terminal width and converting semantic tags to console styles.
 */
class ManualFormatter
{
    // Maximum width for text wrapping, even on very wide terminals
    private const MAX_WIDTH = 120;
    private const TABLE_MIN_COLUMN_WIDTH = 8;
    private const TABLE_PREFERRED_WIDTH_PERCENTILE = 0.75;
    private const FORMATTER_OPEN_TAG_REGEX = '[a-z](?:[^\\\\<>]*+ | \\\\.)*';
    private const FORMATTER_CLOSE_TAG_REGEX = '[a-z][^<>]*+';
    private const PHP_CODE_ROLE = 'php';

    private ManualWrapper $wrapper;
    private int $width;
    private ?ManualInterface $manual;
    private OutputFormatterInterface $outputFormatter;

    /**
     * @param int                  $width  Terminal width for text wrapping
     * @param ManualInterface|null $manual Optional manual for generating hyperlinks
     */
    public function __construct(int $width = 100, ?ManualInterface $manual = null, ?OutputFormatterInterface $outputFormatter = null)
    {
        // Cap width at MAX_WIDTH for readability on ultra-wide terminals
        $this->width = \min($width, self::MAX_WIDTH);
        $this->manual = $manual;
        $this->outputFormatter = $outputFormatter ?? new OutputFormatter();
        if ($outputFormatter === null) {
            (new Theme('modern'))->applyStyles($this->outputFormatter, !Theme::grayExists($this->outputFormatter));
        }

        $this->wrapper = new ManualWrapper($this->outputFormatter);
    }

    /**
     * Format structured manual data for display.
     *
     * @param array $data Structured manual data
     */
    public function format(array $data): string
    {
        $output = [];

        switch ($data['type'] ?? '') {
            case 'function':
                $output[] = $this->formatFunction($data);
                break;
            case 'class':
            case 'language':
                $output[] = $this->formatClass($data);
                break;
            case 'constant':
                $output[] = $this->formatConstant($data);
                break;
            default:
                if ($description = $this->formatDescriptionBody($data)) {
                    $output[] = $description;
                }
        }

        return \implode("\n\n", \array_filter($output))."\n";
    }

    /**
     * Format a function entry.
     *
     * @param array $data Function data
     */
    private function formatFunction(array $data): string
    {
        $output = [];

        if ($description = $this->formatDescriptionBody($data)) {
            $output[] = $description;
        }

        if (!empty($data['params'])) {
            $output[] = $this->formatParameters($data['params']);
        }

        if (!empty($data['return'])) {
            $output[] = $this->formatReturn($data['return']);
        }

        if (!empty($data['seeAlso'])) {
            $output[] = $this->formatSeeAlso($data['seeAlso']);
        }

        return \implode("\n\n", \array_filter($output));
    }

    /**
     * Format a class entry.
     *
     * @param array $data Class data
     */
    private function formatClass(array $data): string
    {
        $output = [];

        if ($description = $this->formatDescriptionBody($data)) {
            $output[] = $description;
        }

        if (!empty($data['seeAlso'])) {
            $output[] = $this->formatSeeAlso($data['seeAlso']);
        }

        return \implode("\n\n", \array_filter($output));
    }

    /**
     * Format a constant entry.
     *
     * @param array $data Constant data
     */
    private function formatConstant(array $data): string
    {
        $output = [];

        if (isset($data['value'])) {
            $output[] = '<strong>Value:</strong> '.$this->thunkTags($data['value']);
        }

        if ($description = $this->formatDescriptionBody($data)) {
            $output[] = $description;
        }

        if (!empty($data['seeAlso'])) {
            $output[] = $this->formatSeeAlso($data['seeAlso']);
        }

        return \implode("\n\n", \array_filter($output));
    }

    /**
     * Format a description section.
     *
     * @param string $description Description text with semantic tags
     *
     * @return string Formatted description
     */
    private function formatDescription(string $description): string
    {
        $output = ['<comment>Description:</comment>'];
        $output = \array_merge($output, $this->formatWrappedText($description, '  '));

        return \implode("\n", $output);
    }

    private function formatDescriptionBody(array $data): ?string
    {
        if ($this->hasStructuredContent($data['content'] ?? [])) {
            return $this->formatContent($data['content']);
        }

        if ($description = $this->descriptionText($data, true)) {
            return $this->formatDescription($description);
        }

        return null;
    }

    /**
     * Format ordered manual content blocks.
     *
     * @param array $content Content blocks from the v3 manual
     */
    private function formatContent(array $content): string
    {
        $output = ['<comment>Description:</comment>'];
        $blocks = $this->formatContentBlocks($content, '  ');

        if (!empty($blocks)) {
            $output[] = \implode("\n\n", $blocks);
        }

        return \implode("\n", $output);
    }

    private function formatContentBlocks(array $content, string $indent): array
    {
        $blocks = [];

        foreach ($content as $block) {
            switch ($block['type'] ?? 'paragraph') {
                case 'heading':
                    if (!empty($block['text'])) {
                        $blocks[] = $indent.'<comment>'.$this->formatInlineText($block['text']).'</comment>';
                    }
                    break;

                case 'table':
                    if ($table = $this->formatTableBlock($block, $indent)) {
                        $blocks[] = $table;
                    }
                    break;

                case 'code':
                    if ($code = $this->formatCodeBlock($block, $indent)) {
                        $blocks[] = $code;
                    }
                    break;

                case 'paragraph':
                default:
                    if (!empty($block['text'])) {
                        $blocks[] = \implode("\n", $this->formatWrappedText($block['text'], $indent));
                    }
                    break;
            }
        }

        return $blocks;
    }

    private function hasStructuredContent(array $content): bool
    {
        if (\count($content) !== 1) {
            return !empty($content);
        }

        return ($content[0]['type'] ?? 'paragraph') !== 'paragraph';
    }

    private function contentParagraphText(array $content): ?string
    {
        if (\count($content) !== 1 || ($content[0]['type'] ?? 'paragraph') !== 'paragraph') {
            return null;
        }

        return $content[0]['text'] ?? null;
    }

    private function formatTableBlock(array $data, string $indent): string
    {
        $headers = \array_map([$this, 'formatInlineText'], $data['headers'] ?? []);
        $rows = \array_map(function ($row) {
            return \array_map([$this, 'formatInlineText'], $row);
        }, $data['rows'] ?? []);

        if (empty($headers) && empty($rows)) {
            return '';
        }

        $columnCount = $this->tableColumnCount($headers, $rows);
        $headers = $this->normalizeTableRow($headers, $columnCount);
        $rows = \array_map(function ($row) use ($columnCount) {
            return $this->normalizeTableRow($row, $columnCount);
        }, $rows);

        $columnWidths = $this->tableColumnWidths($headers, $rows, $columnCount, $indent);
        $headers = $this->wrapTableRow($headers, $columnWidths);
        $rows = \array_map(function ($row) use ($columnWidths) {
            return $this->wrapTableRow($row, $columnWidths);
        }, $rows);

        $output = [];
        if (!empty($data['title'])) {
            $title = OutputFormatter::escape($this->plainInlineText($data['title']).':');
            $titleLines = $this->wrapper->wrap($title, $this->width - $this->displayWidth($indent));
            $output[] = \implode("\n", \array_map(function ($line) use ($indent) {
                return '<comment>'.$indent.$line.'</comment>';
            }, \explode("\n", $titleLines)));
        }

        if (!empty($data['headers'])) {
            $output[] = $this->renderTableRow($headers, $columnWidths, $indent, true);
        }

        foreach ($rows as $row) {
            $output[] = $this->renderTableRow($row, $columnWidths, $indent);
        }

        return \implode("\n", $output);
    }

    private function tableColumnCount(array $headers, array $rows): int
    {
        $columnCount = \count($headers);
        foreach ($rows as $row) {
            $columnCount = \max($columnCount, \count($row));
        }

        return $columnCount;
    }

    private function normalizeTableRow(array $row, int $columnCount): array
    {
        return \array_pad(\array_slice($row, 0, $columnCount), $columnCount, '');
    }

    private function tableColumnWidths(array $headers, array $rows, int $columnCount, string $indent): array
    {
        $maxWidths = \array_fill(0, $columnCount, 1);
        $cellWidths = \array_fill(0, $columnCount, []);
        $wordWidths = \array_fill(0, $columnCount, 1);

        $this->measureTableRow($maxWidths, $cellWidths, $wordWidths, $headers);
        foreach ($rows as $row) {
            $this->measureTableRow($maxWidths, $cellWidths, $wordWidths, $row);
        }

        $budget = $this->tableContentWidth($columnCount, $indent);
        $minWidth = \min(self::TABLE_MIN_COLUMN_WIDTH, \max(1, (int) \floor($budget / $columnCount)));
        $hardMinWidths = \array_map(function (int $wordWidth, int $maxWidth) use ($minWidth) {
            return \min($maxWidth, \max($minWidth, $wordWidth));
        }, $wordWidths, $maxWidths);
        $fitMinWidths = \array_sum($hardMinWidths) <= $budget ? $hardMinWidths : \array_fill(0, $columnCount, $minWidth);
        $minWidths = \array_map(function (array $widths, int $maxWidth) use ($minWidth) {
            return \min($maxWidth, \max($minWidth, $this->tablePercentileWidth($widths, self::TABLE_PREFERRED_WIDTH_PERCENTILE)));
        }, $cellWidths, $maxWidths);
        $minWidths = \array_map('max', $minWidths, $fitMinWidths);

        $widths = $this->fitTableWidthsToBudget($minWidths, $budget, $fitMinWidths);

        return $this->distributeTableWidth($widths, $maxWidths, $budget);
    }

    private function fitTableWidthsToBudget(array $widths, int $budget, array $minWidths): array
    {
        $overage = \array_sum($widths) - $budget;
        while ($overage > 0) {
            $widestColumn = null;
            $widestWidth = 0;

            foreach ($widths as $column => $width) {
                if ($width > $widestWidth && $width > $minWidths[$column]) {
                    $widestColumn = $column;
                    $widestWidth = $width;
                }
            }

            if ($widestColumn === null) {
                break;
            }

            $reduction = \min($overage, $widestWidth - $minWidths[$widestColumn]);
            $widths[$widestColumn] -= $reduction;
            $overage -= $reduction;
        }

        return $widths;
    }

    private function distributeTableWidth(array $widths, array $maxWidths, int $budget): array
    {
        $remaining = $budget - \array_sum($widths);
        while ($remaining > 0) {
            $neediestColumn = null;
            $largestRelativeDeficit = 0.0;

            foreach ($widths as $column => $width) {
                $deficit = $maxWidths[$column] - $width;
                $relativeDeficit = $deficit / $width;
                if ($relativeDeficit > $largestRelativeDeficit) {
                    $neediestColumn = $column;
                    $largestRelativeDeficit = $relativeDeficit;
                }
            }

            if ($neediestColumn === null) {
                break;
            }

            $widths[$neediestColumn]++;
            $remaining--;
        }

        return $widths;
    }

    private function measureTableRow(array &$maxWidths, array &$cellWidths, array &$wordWidths, array $row): void
    {
        foreach ($row as $column => $cell) {
            foreach (\explode("\n", $cell) as $line) {
                $width = $this->displayWidth($line);
                $maxWidths[$column] = \max($maxWidths[$column], $width);
                $cellWidths[$column][] = $width;
                $wordWidths[$column] = \max($wordWidths[$column], $this->wrapper->longestWordWidth($line));
            }
        }
    }

    private function tablePercentileWidth(array $widths, float $percentile): int
    {
        if (empty($widths)) {
            return 1;
        }

        \sort($widths);
        $index = (int) \ceil(\count($widths) * $percentile) - 1;

        return $widths[\max(0, \min($index, \count($widths) - 1))];
    }

    private function tableContentWidth(int $columnCount, string $indent): int
    {
        $tableOverhead = ($columnCount - 1) * 3;

        return \max($columnCount, $this->width - $this->displayWidth($indent) - $tableOverhead);
    }

    private function wrapTableRow(array $row, array $columnWidths): array
    {
        return \array_map(function ($cell, $column) use ($columnWidths) {
            return $this->wrapper->wrap($cell, $columnWidths[$column]);
        }, $row, \array_keys($row));
    }

    private function renderTableRow(array $row, array $columnWidths, string $indent, bool $header = false): string
    {
        $cells = \array_map([$this, 'splitTableCell'], $row);
        $height = \max(\array_map('count', $cells));
        $lines = [];

        for ($line = 0; $line < $height; $line++) {
            $parts = [];
            foreach ($cells as $column => $cellLines) {
                $parts[] = $this->padTableLine($cellLines[$line] ?? '', $columnWidths[$column]);
            }

            $rendered = $indent.\implode('   ', $parts);
            if ($header) {
                $rendered = '<comment>'.$rendered.'</comment>';
            }

            $lines[] = $rendered;
        }

        return \implode("\n", $lines);
    }

    private function splitTableCell(string $cell): array
    {
        if (\strpos($cell, '<') === false) {
            return \explode("\n", $cell);
        }

        return $this->balanceTableCellFormatting(\explode("\n", $cell));
    }

    private function balanceTableCellFormatting(array $lines): array
    {
        $openTags = [];
        $balanced = [];

        foreach ($lines as $line) {
            if (empty($openTags) && \strpos($line, '<') === false) {
                $balanced[] = $line;
                continue;
            }

            $prefix = $this->tableCellFormattingPrefix($openTags);

            $openTags = $this->openFormatterTagsAfterLine($line, $openTags);

            $suffix = $this->tableCellFormattingSuffix($openTags);

            $balanced[] = $prefix.$line.$suffix;
        }

        return $balanced;
    }

    private function openFormatterTagsAfterLine(string $line, array $openTags): array
    {
        if (\strpos($line, '<') === false) {
            return $openTags;
        }

        // Keep this in sync with Symfony's OutputFormatter::formatAndWrap tag matching.
        $tagRegex = \sprintf('#<(?:%s|/(?:%s)?)>#ix', self::FORMATTER_OPEN_TAG_REGEX, self::FORMATTER_CLOSE_TAG_REGEX);
        if (!\preg_match_all($tagRegex, $line, $matches, \PREG_OFFSET_CAPTURE | \PREG_SET_ORDER)) {
            return $openTags;
        }

        foreach ($matches as $match) {
            $offset = $match[0][1];
            if ($offset !== 0 && $line[$offset - 1] === '\\') {
                continue;
            }

            $text = $match[0][0];
            $open = $text[1] !== '/';
            $tag = $open ? \substr($text, 1, -1) : \substr($text, 2, -1);

            if (!$open && $tag === '') {
                \array_pop($openTags);
            } elseif (!$this->hasNamedFormatterStyle($tag)) {
                continue;
            } elseif ($open) {
                $openTags[] = $tag;
            } else {
                $openTags = $this->popFormatterTag($openTags, $tag);
            }
        }

        return $openTags;
    }

    private function tableCellFormattingPrefix(array $openTags): string
    {
        $prefix = '';
        foreach ($openTags as $tag) {
            $prefix .= '<'.$tag.'>';
        }

        return $prefix;
    }

    private function tableCellFormattingSuffix(array $openTags): string
    {
        $suffix = '';
        for ($i = \count($openTags) - 1; $i >= 0; $i--) {
            $suffix .= '</'.$openTags[$i].'>';
        }

        return $suffix;
    }

    private function hasNamedFormatterStyle(string $tag): bool
    {
        return $tag === \strtolower($tag) && $this->outputFormatter->hasStyle($tag);
    }

    private function popFormatterTag(array $openTags, string $tag): array
    {
        for ($i = \count($openTags) - 1; $i >= 0; $i--) {
            if ($openTags[$i] === $tag) {
                return \array_slice($openTags, 0, $i);
            }
        }

        return $openTags;
    }

    private function padTableLine(string $line, int $width): string
    {
        return $line.$this->displayPadding($line, $width);
    }

    private function displayWidth(string $text): int
    {
        return DisplayString::widthWithoutFormatting($text, $this->outputFormatter);
    }

    private function displayPadding(string $text, int $width): string
    {
        return \str_repeat(' ', \max(0, $width - $this->displayWidth($text)));
    }

    private function formatCodeBlock(array $data, string $indent): string
    {
        if (empty($data['text'])) {
            return '';
        }

        $text = \str_replace(["\r\n", "\r"], "\n", $data['text']);
        $role = \strtolower((string) ($data['role'] ?? ''));

        if ($role !== self::PHP_CODE_ROLE && $role !== '') {
            $text = OutputFormatter::escape($text);
        } elseif ($this->startsWithPhpOpenTag($text)) {
            $text = CodeFormatter::formatCodeBlock($text);
        } elseif ($role === self::PHP_CODE_ROLE) {
            $text = CodeFormatter::formatSnippetCodeBlock($text);
        } else {
            $text = OutputFormatter::escape($text);
        }

        $lines = \explode("\n", $text);
        $lines = \array_map(function ($line) use ($indent) {
            return $indent.\rtrim($line);
        }, $lines);

        return \implode("\n", $lines);
    }

    private function startsWithPhpOpenTag(string $text): bool
    {
        return \preg_match('/^\s*<\?(?:php\b|=)/i', $text) === 1;
    }

    /**
     * Format parameters section.
     *
     * @param array $params Parameter list
     */
    private function formatParameters(array $params): string
    {
        if ($this->hasStructuredParameterContent($params)) {
            return $this->formatParametersStacked($params);
        }

        // Decide layout based on terminal width
        // Use table layout for wide terminals (80+), stacked for narrow
        if ($this->width >= 80) {
            return $this->formatParametersTable($params);
        } else {
            return $this->formatParametersStacked($params);
        }
    }

    /**
     * Format parameters as a table (for wide terminals).
     *
     * @param array $params Parameter list
     */
    private function formatParametersTable(array $params): string
    {
        $output = ['<comment>Param:</comment>'];

        $typeWidth = \max(\array_map(function ($param) {
            return $this->displayWidth($param['type'] ?? 'mixed');
        }, $params));

        $nameWidth = \max(\array_map(function ($param) {
            return $this->displayWidth($param['name']);
        }, $params));

        // Build columns with padding OUTSIDE style tags
        $indent = \str_repeat(' ', $typeWidth + $nameWidth + 6);
        $wrapWidth = $this->width - $this->displayWidth($indent);

        foreach ($params as $param) {
            $type = $param['type'] ?? 'mixed';
            $name = $param['name'];
            $desc = $this->descriptionText($param) ?? '';

            // Wrap in style tags first, THEN pad to avoid long color blocks
            $typeFormatted = $this->formatType($type).$this->displayPadding($type, $typeWidth);
            $nameFormatted = '<strong>'.$name.'</strong>'.$this->displayPadding($name, $nameWidth);

            // Wrap description with proper indentation
            if (!empty($desc)) {
                $firstLine = '  '.$typeFormatted.'  '.$nameFormatted.'  ';
                $output = \array_merge($output, $this->formatWrappedText($desc, $indent, $firstLine, $wrapWidth));
            } else {
                $output[] = '  '.$typeFormatted.'  '.$nameFormatted;
            }
        }

        return \implode("\n", $output);
    }

    private function hasStructuredParameterContent(array $params): bool
    {
        foreach ($params as $param) {
            if ($this->hasStructuredContent($param['content'] ?? [])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Format parameters stacked (for narrow terminals).
     *
     * @param array $params Parameter list
     */
    private function formatParametersStacked(array $params): string
    {
        $output = ['<comment>Param:</comment>'];

        $typeWidth = \max(\array_map(function ($param) {
            return $this->displayWidth($param['type'] ?? 'mixed');
        }, $params));

        foreach ($params as $param) {
            $type = $param['type'] ?? 'mixed';
            $name = $param['name'];

            $output[] = \sprintf('  %s%s  <strong>%s</strong>', $this->formatType($type), $this->displayPadding($type, $typeWidth), $name);

            if ($this->hasStructuredContent($param['content'] ?? [])) {
                $blocks = $this->formatContentBlocks($param['content'], '  ');
                if (!empty($blocks)) {
                    $output[] = \implode("\n\n", $blocks);
                }
            } elseif ($desc = $this->descriptionText($param)) {
                $indent = \str_repeat(' ', $typeWidth + 4);
                $output = \array_merge($output, $this->formatWrappedText($desc, $indent));
            }
        }

        return \implode("\n", $output);
    }

    /**
     * Format return value section.
     *
     * @param array $return Return value data
     */
    private function formatReturn(array $return): string
    {
        $output = ['<comment>Return:</comment>'];

        $type = $return['type'] ?? 'unknown';
        $desc = $this->descriptionText($return) ?? '';
        $formattedType = $this->formatType($type);

        if ($this->hasStructuredContent($return['content'] ?? [])) {
            $output[] = \sprintf('  %s', $formattedType);
            $blocks = $this->formatContentBlocks($return['content'], '  ');
            if (!empty($blocks)) {
                $output[] = \implode("\n\n", $blocks);
            }

            return \implode("\n", $output);
        }

        $indent = \str_repeat(' ', $this->displayWidth($type) + 4);
        $wrapWidth = $this->width - $this->displayWidth($indent);

        if (!empty($desc)) {
            $firstLine = \sprintf('  %s  ', $formattedType);
            $output = \array_merge($output, $this->formatWrappedText($desc, $indent, $firstLine, $wrapWidth));
        } else {
            $output[] = \sprintf('  %s', $formattedType);
        }

        return \implode("\n", $output);
    }

    private function formatType(string $type): string
    {
        if (\strpos($type, '|') === false) {
            return '<info>'.$type.'</info>';
        }

        return \implode('|', \array_map(function (string $part): string {
            return '<info>'.$part.'</info>';
        }, \explode('|', $type)));
    }

    /**
     * Format see also section.
     *
     * @param array $seeAlso List of related functions/classes
     */
    private function formatSeeAlso(array $seeAlso): string
    {
        if (empty($seeAlso)) {
            return '';
        }

        $output = ['<comment>See Also:</comment>'];

        // Format items with hyperlinks if manual is available
        $items = \array_map(function ($item) {
            return $this->formatSeeAlsoItem($item);
        }, $seeAlso);

        // Don't wrap - console tags need to stay intact
        // Just join with commas and indent
        $output[] = '  '.\implode(', ', $items);

        return \implode("\n", $output);
    }

    /**
     * Format a single see also item with hyperlink if available.
     *
     * @param string $item Function or class name (may contain XML tags)
     */
    private function formatSeeAlsoItem(string $item): string
    {
        $cleanItem = \strip_tags($item);

        $href = null;
        if ($this->manual !== null && $this->manual->get($cleanItem) !== null) {
            $href = LinkFormatter::getPhpNetUrl($cleanItem);
        }

        $displayText = $cleanItem;
        if (\strpos($item, '<function>') !== false) {
            $displayText .= '()';
        }

        if ($href !== null) {
            return LinkFormatter::styleWithHref('info', $displayText, $href);
        }

        $formatted = $this->thunkTags($item);
        if (\strpos($item, '<function>') !== false && \strpos($formatted, '()') === false) {
            $formatted .= '()';
        }

        return $formatted;
    }

    /**
     * Indent wrapped text lines.
     *
     * Takes wrapped text and adds indentation to each line.
     * The first line can have a different prefix than subsequent lines.
     *
     * @param string      $wrapped     Wrapped text (may contain newlines)
     * @param string      $indent      Indentation for continuation lines
     * @param string|null $firstIndent Optional different indentation for first line (defaults to $indent)
     *
     * @return array Lines with indentation applied
     */
    private function indentWrappedLines(string $wrapped, string $indent, ?string $firstIndent = null): array
    {
        $firstIndent = $firstIndent ?? $indent;
        $lines = \explode("\n", $wrapped);
        $output = [];

        foreach ($lines as $i => $line) {
            $output[] = ($i === 0 ? $firstIndent : $indent).$line;
        }

        return $output;
    }

    private function formatWrappedText(string $text, string $indent, ?string $firstIndent = null, ?int $width = null): array
    {
        $text = $this->thunkTags($text);
        $width = $width ?? $this->width - $this->displayWidth($indent);
        $wrapped = $this->wrapper->wrap($text, $width);

        return $this->indentWrappedLines($wrapped, $indent, $firstIndent);
    }

    private function formatInlineText(string $text): string
    {
        return $this->thunkTags($text);
    }

    private function plainInlineText(string $text): string
    {
        return Helper::removeDecoration($this->outputFormatter, $this->thunkTags($text));
    }

    private function descriptionText(array $data, bool $preferContent = false): ?string
    {
        $content = $this->contentParagraphText($data['content'] ?? []);

        return $preferContent ? ($content ?? $data['description'] ?? null) : ($data['description'] ?? $content);
    }

    /**
     * Convert semantic XML tags to Symfony Console format tags.
     *
     * @param string $text Text with semantic tags
     *
     * @return string Text with console format tags
     */
    private function thunkTags(string $text): string
    {
        // First, escape any < and > that aren't part of our semantic tags
        // Protect our semantic tags by replacing them with placeholders
        $tagMap = [];
        $tagIndex = 0;

        // Protect semantic tags
        $semanticTags = ['parameter', 'function', 'constant', 'classname', 'type', 'literal', 'class'];
        foreach ($semanticTags as $tag) {
            $text = \preg_replace_callback(
                "/<{$tag}>|<\/{$tag}>/",
                function ($matches) use (&$tagMap, &$tagIndex) {
                    $placeholder = "\x00TAG{$tagIndex}\x00";
                    $tagMap[$placeholder] = $matches[0];
                    $tagIndex++;

                    return $placeholder;
                },
                $text
            );
        }

        // Now escape any remaining < and > (these are content, not tags)
        $text = \str_replace(['<', '>'], ['\\<', '\\>'], $text);

        // Restore protected tags
        $text = \str_replace(\array_keys($tagMap), \array_values($tagMap), $text);

        // Handle parameters: add $ prefix and make bold
        $text = \preg_replace_callback(
            '/<parameter>([^<]+)<\/parameter>/',
            function ($matches) {
                $name = $matches[1];
                // Add $ if not already present
                if ($name[0] !== '$') {
                    $name = '$'.$name;
                }

                return '<strong>'.$name.'</strong>';
            },
            $text
        );

        // Handle functions: add () suffix and make bold
        $text = \preg_replace_callback(
            '/<function>([^<]+)<\/function>/',
            function ($matches) {
                $name = $matches[1];
                // Add () if not already present
                if (\substr($name, -2) !== '()') {
                    $name .= '()';
                }

                return '<strong>'.$name.'</strong>';
            },
            $text
        );

        // Map other semantic tags to corresponding formats
        $replacements = [
            '<constant>'   => '<info>',
            '</constant>'  => '</info>',
            '<classname>'  => '<class>',
            '</classname>' => '</class>',
            '<class>'      => '<class>',
            '</class>'     => '</class>',
            '<type>'       => '<info>',
            '</type>'      => '</info>',
            '<literal>'    => '<return>',
            '</literal>'   => '</return>',
        ];

        $text = \str_replace(\array_keys($replacements), \array_values($replacements), $text);

        return $text;
    }
}
