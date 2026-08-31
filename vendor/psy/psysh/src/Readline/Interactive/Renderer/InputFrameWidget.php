<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Renderer;

use Psy\Formatter\CodeFormatter;
use Psy\Output\Theme;
use Psy\Readline\Interactive\Helper\CommandHighlighter;
use Psy\Readline\Interactive\Input\Buffer;
use Psy\Readline\Interactive\Input\History;
use Psy\Readline\Interactive\Layout\DisplayString;
use Psy\Readline\Interactive\Suggestion\SuggestionResult;
use Psy\Readline\Interactive\Terminal;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 * The input frame widget: prompt(s), buffer text, ghost-text suggestion,
 * in-frame history, and the cursor position.
 *
 * Owns its rendering state (theme, error mode, syntax highlighting, command
 * highlighter, history) and is mutated across renders. Per-render inputs
 * (buffer, suggestion, history-search term, command flag, whether an
 * overlay is active) are supplied via setRenderState() before each render.
 */
class InputFrameWidget implements WidgetInterface
{
    private const CLEAR_TO_END_OF_LINE = "\033[K";
    private const INPUT_FRAME_PADDING_ROWS = 2;

    private Terminal $terminal;
    private LineMetrics $lineMetrics;
    private Theme $theme;
    private CommandHighlighter $commandHighlighter;

    private bool $useSyntaxHighlighting = true;
    private bool $errorMode = false;

    /** @var string[] Previously submitted lines rendered above the current input. */
    private array $historyLines = [];

    /** @var array<string, int> Cached prompt widths keyed by "line:decorated". */
    private array $promptWidthCache = [];

    private ?Buffer $buffer = null;
    private ?SuggestionResult $suggestion = null;
    private ?string $historySearchTerm = null;
    private bool $isCommand = false;
    private bool $overlayActive = false;

    public function __construct(Terminal $terminal, LineMetrics $lineMetrics, ?Theme $theme = null)
    {
        $this->terminal = $terminal;
        $this->lineMetrics = $lineMetrics;
        $this->theme = $theme ?? new Theme();
        $this->commandHighlighter = new CommandHighlighter();
    }

    public function getCommandHighlighter(): CommandHighlighter
    {
        return $this->commandHighlighter;
    }

    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
        $this->promptWidthCache = [];
    }

    public function setUseSyntaxHighlighting(bool $enabled): void
    {
        $this->useSyntaxHighlighting = $enabled;
    }

    public function setErrorMode(bool $error): void
    {
        $this->errorMode = $error;
    }

    public function isCompact(): bool
    {
        return $this->theme->compact();
    }

    /**
     * Get number of outer rows surrounding input content (top + bottom padding).
     */
    public function getOuterRowCount(): int
    {
        return $this->theme->compact() ? 0 : self::INPUT_FRAME_PADDING_ROWS;
    }

    /**
     * Add a previously submitted input to the in-frame history.
     */
    public function addHistoryLines(string $text, bool $isCommand = false): void
    {
        $newLines = $this->formatHighlightedLinesWithPrompts($this->formatInputLines($text, null, $isCommand));
        \array_push($this->historyLines, ...$newLines);
    }

    /**
     * Clear previously submitted lines rendered above the current input.
     */
    public function clearHistoryLines(): void
    {
        $this->historyLines = [];
    }

    /**
     * Get active prompt display width for the cursor's current line.
     */
    public function getPromptWidthForCurrentLine(Buffer $buffer): int
    {
        $lineNumber = 0;
        if (\strpos($buffer->getText(), "\n") !== false) {
            $lineNumber = $buffer->getCurrentLineNumber();
        }

        return $this->getPromptWidthForLine($lineNumber, $this->terminal->getFormatter());
    }

    /**
     * Get the prompt string for a given line number.
     *
     * Exposed so other renderers (e.g. the search-frame mode in
     * FrameRenderer) can compose the same prompt without re-implementing it.
     */
    public function getPromptForLine(int $lineNumber): string
    {
        return $lineNumber === 0 ? $this->theme->prompt() : $this->theme->bufferPrompt();
    }

    /**
     * Get the display width of the prompt for a given line number.
     */
    public function getPromptWidthForLine(int $lineNumber, ?OutputFormatterInterface $formatter = null): int
    {
        $key = ($lineNumber === 0 ? '0' : '1').($formatter !== null ? ':f' : ':p');
        if (isset($this->promptWidthCache[$key])) {
            return $this->promptWidthCache[$key];
        }

        $prompt = $this->getPromptForLine($lineNumber);
        $width = ($formatter === null)
            ? DisplayString::width($prompt)
            : DisplayString::widthWithoutFormatting($prompt, $formatter);

        $this->promptWidthCache[$key] = $width;

        return $width;
    }

    /**
     * Wrap content lines in the input frame (background, padding, history).
     *
     * Exposed so renderSearchFrame can reuse it for the preview line.
     *
     * @param string[] $contentLines
     *
     * @return string[]
     */
    public function wrapContentInFrame(array $contentLines): array
    {
        if ($this->theme->compact()) {
            return \array_merge($this->historyLines, $contentLines);
        }

        $styleName = $this->errorMode ? 'input_frame_error' : 'input_frame';
        $formatter = $this->terminal->getFormatter();
        $inputFrameStyle = ($formatter->isDecorated() && $formatter->hasStyle($styleName))
            ? $formatter->getStyle($styleName)
            : null;

        $framedLines = [''];
        foreach (['', ...$this->historyLines, ...$contentLines, ''] as $line) {
            $lineWithClear = $line.self::CLEAR_TO_END_OF_LINE;
            $framedLines[] = $inputFrameStyle ? $inputFrameStyle->apply($lineWithClear) : $lineWithClear;
        }
        $framedLines[] = '';

        return $framedLines;
    }

    /**
     * Set the per-render inputs.
     *
     * Must be called immediately before render(); subsequent renders
     * pick up new state via another call.
     */
    public function setRenderState(
        Buffer $buffer,
        ?SuggestionResult $suggestion = null,
        ?string $historySearchTerm = null,
        bool $isCommand = false,
        bool $overlayActive = false
    ): void {
        $this->buffer = $buffer;
        $this->suggestion = $suggestion;
        $this->historySearchTerm = $historySearchTerm;
        $this->isCommand = $isCommand;
        $this->overlayActive = $overlayActive;
    }

    /**
     * Reset state at the start of a new readline session.
     */
    public function reset(): void
    {
        $this->historyLines = [];
        $this->errorMode = false;
        $this->buffer = null;
        $this->suggestion = null;
        $this->historySearchTerm = null;
        $this->isCommand = false;
        $this->overlayActive = false;
    }

    /**
     * {@inheritdoc}
     *
     * The widget appends its input-frame lines, sets the cursor on the
     * frame, and returns the number of wrapped terminal rows consumed.
     * Area is currently informational; the input frame uses whatever
     * vertical space the buffer + history requires.
     */
    public function render(Frame $frame, Area $area): int
    {
        if ($this->buffer === null) {
            throw new \LogicException('InputFrameWidget::render() requires setRenderState() to be called first.');
        }

        $buffer = $this->buffer;
        $isMultiline = \strpos($buffer->getText(), "\n") !== false;
        $inputLines = $this->buildInputLines($buffer, $isMultiline);

        foreach ($inputLines as $line) {
            $frame->appendLine($line);
        }

        [$cursorRow, $cursorColumn] = $this->getCursorPosition($buffer, $isMultiline);
        $frame->setCursor($cursorRow, $cursorColumn);

        return $this->lineMetrics->frameRowCount($inputLines);
    }

    /**
     * Build the input section of the frame.
     *
     * @return string[]
     */
    private function buildInputLines(Buffer $buffer, bool $isMultiline): array
    {
        $text = $buffer->getText();
        $contentLines = [];
        if ($isMultiline) {
            $contentLines = $this->formatHighlightedLinesWithPrompts(
                $this->formatInputLines($text, $this->historySearchTerm, $this->isCommand),
            );
        } else {
            $line = $this->getPromptForLine(0).\implode(
                "\n",
                $this->formatInputLines($text, $this->historySearchTerm, $this->isCommand),
            );

            if ($this->suggestion !== null) {
                $line = $this->appendSuggestionGhostText($line, $buffer, $text, $this->suggestion);
            }

            $contentLines[] = $line;
        }

        return $this->wrapContentInFrame($contentLines);
    }

    /**
     * Prefix each formatted line with the appropriate prompt for its index.
     *
     * @param string[] $lines
     *
     * @return string[]
     */
    private function formatHighlightedLinesWithPrompts(array $lines): array
    {
        $result = [];
        foreach ($lines as $i => $line) {
            $result[] = $this->getPromptForLine($i).$line;
        }

        return $result;
    }

    /**
     * Highlight all occurrences of a search term in text.
     *
     * Uses smart case (case-insensitive unless the term contains uppercase).
     */
    private function highlightSearchTerm(string $text, string $term): string
    {
        $formatter = $this->terminal->getFormatter();
        if (!$formatter->isDecorated() || !$formatter->hasStyle('input_highlight')) {
            return $text;
        }

        $style = $formatter->getStyle('input_highlight');
        $pattern = '/'.\preg_quote($term, '/').'/u'.(History::isSearchCaseSensitive($term) ? '' : 'i');
        $highlighted = \preg_replace_callback($pattern, fn (array $match) => $style->apply($match[0]), $text);

        return $highlighted ?? $text;
    }

    /**
     * Append single-line suggestion ghost text when there is room.
     */
    private function appendSuggestionGhostText(string $line, Buffer $buffer, string $text, SuggestionResult $suggestion): string
    {
        // Completion overlays own the viewport; don't mix ghost text with menus.
        if ($this->overlayActive) {
            return $line;
        }

        $absoluteCursorColumn = $this->getPromptWidthForLine(0, $this->terminal->getFormatter())
            + DisplayString::width(\mb_substr($text, 0, $buffer->getCursor())) + 1;
        $cursorColumn = $this->lineMetrics->softWrap()->normalizeColumn($absoluteCursorColumn);
        $maxWidth = $this->lineMetrics->getTerminalWidth() - $cursorColumn + 1;
        if ($maxWidth <= 0) {
            return $line;
        }

        $suggestionText = DisplayString::truncate($suggestion->getDisplayText(), $maxWidth, true);
        if ($suggestionText === '') {
            return $line;
        }

        return $line.$this->terminal->format('<whisper>'.OutputFormatter::escape($suggestionText).'</whisper>');
    }

    /**
     * Format input text into ANSI-safe lines (highlighted as PHP, command, or raw).
     *
     * @return string[]
     */
    private function formatInputLines(string $text, ?string $historySearchTerm, bool $isCommand): array
    {
        if ($text === '') {
            return [''];
        }

        if ($historySearchTerm !== null) {
            return \explode("\n", $this->highlightSearchTerm($text, $historySearchTerm));
        }

        if (!$this->useSyntaxHighlighting) {
            return \explode("\n", $text);
        }

        if ($isCommand) {
            return $this->commandHighlighter->highlightLines($text, $this->terminal->getFormatter());
        }

        return CodeFormatter::formatInputLines($text, $this->terminal->getFormatter());
    }

    /**
     * Get the wrapped frame row and terminal column where the cursor should be.
     *
     * @return array{int, int}
     */
    private function getCursorPosition(Buffer $buffer, bool $isMultiline): array
    {
        $text = $buffer->getText();
        $historyRowOffset = $this->historyRowCount();
        $calculator = $this->lineMetrics->softWrap();

        if ($isMultiline) {
            $lines = \explode("\n", $text);
            $lineNum = $buffer->getCurrentLineNumber();
            $promptWidth = $this->getPromptWidthForLine($lineNum, $this->terminal->getFormatter());

            $charsBeforeLine = 0;
            $rowsBeforeLine = 0;
            for ($i = 0; $i < $lineNum; $i++) {
                $charsBeforeLine += \mb_strlen($lines[$i]) + 1;
                $rowsBeforeLine += $this->lineMetrics->lineRowCount($this->getPromptForLine($i).($lines[$i] ?? ''));
            }

            $lineText = $lines[$lineNum] ?? '';
            $cursorInLine = \max(0, \min(\mb_strlen($lineText), $buffer->getCursor() - $charsBeforeLine));
            $textBeforeCursor = \mb_substr($lineText, 0, $cursorInLine);

            $absoluteColumn = $promptWidth + DisplayString::width($textBeforeCursor) + 1;

            return [
                $this->getOuterRowCount() + $historyRowOffset + $rowsBeforeLine + $calculator->rowOffsetForAbsoluteColumn($absoluteColumn),
                $calculator->normalizeColumn($absoluteColumn),
            ];
        }

        $textBeforeCursor = \mb_substr($text, 0, $buffer->getCursor());
        $absoluteColumn = $this->getPromptWidthForLine(0, $this->terminal->getFormatter())
            + DisplayString::width($textBeforeCursor) + 1;

        return [
            $this->getOuterRowCount() + $historyRowOffset + $calculator->rowOffsetForAbsoluteColumn($absoluteColumn),
            $calculator->normalizeColumn($absoluteColumn),
        ];
    }

    /**
     * Sum of wrapped rows for accumulated history lines.
     *
     * Computed lazily; LineMetrics caches per-line counts so repeated
     * calls cost little.
     */
    private function historyRowCount(): int
    {
        $count = 0;
        foreach ($this->historyLines as $line) {
            $count += $this->lineMetrics->lineRowCount($line);
        }

        return $count;
    }
}
