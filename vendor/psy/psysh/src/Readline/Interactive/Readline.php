<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive;

use Psy\Completion\CompletionEngine;
use Psy\Exception\BreakException;
use Psy\Output\Theme;
use Psy\Readline\Interactive\Actions\ExpandHistoryOnTabAction;
use Psy\Readline\Interactive\Actions\HistoryExpansionAction;
use Psy\Readline\Interactive\Actions\InsertIndentOnTabAction;
use Psy\Readline\Interactive\Actions\NextHistoryAction;
use Psy\Readline\Interactive\Actions\PreviousHistoryAction;
use Psy\Readline\Interactive\Actions\SelfInsertAction;
use Psy\Readline\Interactive\Actions\TabAction;
use Psy\Readline\Interactive\Input\Buffer;
use Psy\Readline\Interactive\Input\History;
use Psy\Readline\Interactive\Input\InputQueue;
use Psy\Readline\Interactive\Input\Key;
use Psy\Readline\Interactive\Input\KeyBindings;
use Psy\Readline\Interactive\Renderer\FrameRenderer;
use Psy\Readline\Interactive\Renderer\OverlayViewport;
use Psy\Readline\Interactive\Renderer\WidgetInterface;
use Psy\Readline\Interactive\Suggestion\SuggestionEngine;
use Psy\Readline\Interactive\Suggestion\SuggestionResult;
use Psy\Shell;

/**
 * Interactive readline implementation.
 *
 * A pure-PHP readline implementation with better control over cursor
 * positioning, tab completion display, and terminal interaction.
 */
class Readline
{
    private Terminal $terminal;
    private InputQueue $inputQueue;
    private KeyBindings $bindings;
    private History $history;
    private HistorySearch $search;
    private bool $multilineMode = false;
    private ?Shell $shell = null;
    private bool $requireSemicolons = false;
    private Theme $theme;

    private ?TabAction $tabAction = null;
    private ?ExpandHistoryOnTabAction $expandHistoryAction = null;

    private bool $smartBrackets = true;

    private bool $continueFrame = false;
    private int $lastSubmitEscapeRows = 0;
    private ?string $lastSubmittedText = null;

    private bool $useSuggestions = false;
    private SuggestionEngine $suggestionEngine;
    private OverlayViewport $overlayViewport;
    private FrameRenderer $frameRenderer;
    private ?SuggestionResult $currentSuggestion = null;

    /** @var ReadlineMode[] Stack of active modes; top of stack handles input + rendering. */
    private array $modeStack = [];

    /**
     * Create a new interactive Readline instance.
     */
    public function __construct(Terminal $terminal, ?KeyBindings $bindings = null, ?History $history = null)
    {
        $this->terminal = $terminal;
        $this->inputQueue = new InputQueue($this->terminal);
        $this->history = $history ?? new History();
        $this->theme = new Theme();

        $this->suggestionEngine = new SuggestionEngine($this->history);
        $this->overlayViewport = new OverlayViewport($this->terminal);
        $this->frameRenderer = new FrameRenderer($this->terminal, $this->overlayViewport, $this->theme);
        $this->search = new HistorySearch($this->terminal, $this->history, $this->frameRenderer, $this->overlayViewport, $this->theme);

        $this->bindings = $bindings ?? KeyBindings::createDefault($this->history, $this->search, $this->smartBrackets);
    }

    /**
     * Set the theme for prompt strings and compact mode.
     */
    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
        $this->frameRenderer->setTheme($theme);
        $this->search->setTheme($theme);
    }

    /**
     * Check whether compact input frame rendering is enabled.
     */
    public function isInputFrameCompact(): bool
    {
        return $this->frameRenderer->isCompactInputFrame();
    }

    /**
     * Get number of outer rows surrounding input content.
     */
    public function getInputFrameOuterRowCount(): int
    {
        return $this->frameRenderer->getInputFrameOuterRowCount();
    }

    /**
     * Get active prompt display width for the cursor's current line.
     */
    public function getPromptWidthForCurrentLine(Buffer $buffer): int
    {
        return $this->frameRenderer->getPromptWidthForCurrentLine($buffer);
    }

    /**
     * Set the Shell reference.
     */
    public function setShell(Shell $shell): void
    {
        $this->shell = $shell;

        if ($this->expandHistoryAction !== null) {
            $this->expandHistoryAction->setHistoryExpansion(new HistoryExpansionAction($this->history, $this->shell));
        }
    }

    /**
     * Get the command highlighter for CommandAware registration.
     */
    public function getCommandHighlighter(): Helper\CommandHighlighter
    {
        return $this->frameRenderer->getCommandHighlighter();
    }

    /**
     * Set whether to require semicolons on all statements.
     *
     * By default, PsySH automatically inserts semicolons. When set to true,
     * semicolons are strictly required.
     */
    public function setRequireSemicolons(bool $require): void
    {
        $this->requireSemicolons = $require;
    }

    /**
     * Set the CompletionEngine for context-aware tab completion.
     */
    public function setCompletionEngine(CompletionEngine $completionEngine): void
    {
        if ($this->tabAction === null) {
            $this->tabAction = new TabAction($completionEngine, $this->smartBrackets);
            $this->expandHistoryAction = new ExpandHistoryOnTabAction(new HistoryExpansionAction($this->history, $this->shell));
            $this->bindings->bind(
                'control:i',
                new InsertIndentOnTabAction(),
                $this->expandHistoryAction,
                $this->tabAction
            );
        } else {
            $this->tabAction->setCompleter($completionEngine);
        }
    }

    /**
     * Check if the current input is a PsySH command.
     */
    public function isCommand(string $input): bool
    {
        if ($this->shell === null) {
            return false;
        }

        if (\preg_match('/([^\s]+?)(?:\s|$)/A', \ltrim($input), $match)) {
            return $this->shell->has($match[1]);
        }

        return false;
    }

    /**
     * Check if the input should be highlighted as a command (not in an open string or comment).
     */
    private function isCommandInput(string $text): bool
    {
        return $this->isCommand($text) && !$this->isInOpenStringOrComment($text);
    }

    /**
     * Check if currently in multi-line mode.
     */
    public function isMultilineMode(): bool
    {
        return $this->multilineMode;
    }

    /**
     * Check if input is in an open string or comment.
     */
    public function isInOpenStringOrComment(string $input): bool
    {
        $tokens = @\token_get_all('<?php '.$input);
        $last = \array_pop($tokens);

        return $last === '"' || $last === '`' ||
            (\is_array($last) && \in_array($last[0], [\T_ENCAPSED_AND_WHITESPACE, \T_START_HEREDOC, \T_COMMENT]));
    }

    /**
     * Enter multi-line mode.
     */
    public function enterMultilineMode(): void
    {
        $this->multilineMode = true;
    }

    /**
     * Exit multi-line mode.
     */
    public function exitMultilineMode(): void
    {
        $this->multilineMode = false;
    }

    /**
     * Cancel multi-line mode without executing.
     */
    public function cancelMultilineMode(): void
    {
        if (!$this->multilineMode) {
            return;
        }

        $this->multilineMode = false;
    }

    /**
     * Sync multiline mode with buffer content and invalidate the frame on transition.
     */
    private function syncMultilineMode(string $text): void
    {
        $isMultiline = \strpos($text, "\n") !== false;

        if ($this->multilineMode === $isMultiline) {
            return;
        }

        if ($isMultiline) {
            $this->enterMultilineMode();
        } else {
            $this->exitMultilineMode();
        }

        $this->terminal->invalidateFrame();
    }

    /**
     * Read a line of input.
     *
     * @return string|false The input line, or false on EOF
     */
    public function readline()
    {
        while ($this->popMode() !== null) {
            // Defensive: pop any leftover modes from a previous readline()
            // that exited via BreakException or similar.
        }
        $this->clearSuggestion();

        if ($this->continueFrame && $this->lastSubmittedText !== null) {
            // Rewind the cursor past the escape newlines written by SubmitLineAction.
            // Wrap in a frame render so the movement doesn't mark the terminal dirty
            // (we're restoring the cursor to its known position, not making out-of-band changes).
            $this->terminal->beginFrameRender();
            if ($this->lastSubmitEscapeRows > 0) {
                $this->terminal->moveCursorUp($this->lastSubmitEscapeRows);
            }
            $this->terminal->endFrameRender();

            $this->frameRenderer->addHistoryLines($this->lastSubmittedText, $this->isCommandInput($this->lastSubmittedText));

            $this->continueFrame = false;
        } else {
            $this->frameRenderer->reset();
        }

        $this->multilineMode = false;

        $buffer = new Buffer($this->requireSemicolons);
        $this->display($buffer);

        try {
            while (true) {
                $key = $this->inputQueue->read();

                if ($key->isEof()) {
                    $this->escapeCurrentFrameForAbort($buffer);

                    return false;
                }

                // Any keystroke clears error mode
                $this->setInputFrameError(false);

                if ($key->isPaste()) {
                    $this->handlePastedContent($key->getValue(), $buffer);
                    $this->syncMultilineMode($buffer->getText());
                    $this->display($buffer);
                    continue;
                }

                $mode = $this->activeMode();
                if ($mode !== null) {
                    $result = $mode->handleKey($key, $buffer);
                    if ($result === true) {
                        $mode->display($buffer);
                    } else {
                        $this->popMode();
                        if ($result === null) {
                            $this->replayKey($key);
                        }
                        $this->syncMultilineMode($buffer->getText());
                        $this->display($buffer);
                    }
                    continue;
                }

                $action = $this->bindings->get($key);

                if ($action === null && $key->isChar()) {
                    $action = new SelfInsertAction($key->getValue());
                }

                if ($action !== null) {
                    $textBefore = $buffer->getText();
                    $continue = $action->execute($buffer, $this->terminal, $this);

                    if ($continue) {
                        // Any buffer mutation outside of history navigation exits history mode.
                        if ($this->history->isInHistory()
                            && $buffer->getText() !== $textBefore
                            && !$action instanceof PreviousHistoryAction
                            && !$action instanceof NextHistoryAction
                        ) {
                            $this->history->reset();
                        }

                        $mode = $this->activeMode();
                        if ($mode !== null) {
                            $mode->display($buffer);
                        } else {
                            $this->syncMultilineMode($buffer->getText());
                            $this->updateSuggestion($buffer);
                            $this->display($buffer);
                        }
                    } else {
                        $line = $buffer->getText();
                        $this->clearSuggestion();

                        if (!$this->isCommand($line) || $this->isInOpenStringOrComment($line)) {
                            $this->exitMultilineMode();
                        }

                        $this->history->reset();
                        $this->lastSubmittedText = $line;

                        return $line;
                    }
                } else {
                    $this->terminal->bell();
                }
            }
        } catch (BreakException $e) {
            // Shell.php will write the newline
            return false;
        }
    }

    /**
     * Handle pasted content (potentially multi-line).
     */
    private function handlePastedContent(string $content, Buffer $buffer): void
    {
        $content = \strtr(\str_replace("\r\n", "\n", $content), "\r", "\n");

        $buffer->insert($content);
    }

    /**
     * Display the prompt and buffer.
     */
    private function display(Buffer $buffer): void
    {
        $searchTerm = $this->history->isInHistory() ? $this->history->getSearchTerm() : null;
        $text = $buffer->getText();
        $this->frameRenderer->render($buffer, $this->currentSuggestion, $searchTerm, $this->isCommandInput($text));
    }

    /**
     * Set (or clear) the overlay widget and redraw the frame.
     */
    public function setOverlay(Buffer $buffer, ?WidgetInterface $widget): void
    {
        $this->frameRenderer->setOverlay($widget);
        $this->display($buffer);
    }

    /**
     * Clear the overlay and re-render.
     */
    public function clearOverlay(Buffer $buffer): void
    {
        $this->setOverlay($buffer, null);
    }

    /**
     * Read the next key event from the queue.
     */
    public function readNextKey(): Key
    {
        return $this->inputQueue->read();
    }

    /**
     * Replay a key event so the main loop can consume it.
     */
    public function replayKey(Key $key): void
    {
        $this->inputQueue->replay($key);
    }

    /**
     * Internal accessor for interactive readline internals and tests.
     *
     * @internal
     */
    public function getTabAction(): ?TabAction
    {
        return $this->tabAction;
    }

    /**
     * Get available overlay rows for completion/search UI.
     */
    public function getOverlayAvailableRows(bool $collapsed): int
    {
        return $this->overlayViewport->getAvailableRows($collapsed);
    }

    /**
     * Set the input frame error state (red-tinted background for syntax errors).
     */
    public function setInputFrameError(bool $error): void
    {
        $this->frameRenderer->setErrorMode($error);
    }

    /**
     * Clear previously submitted lines from the current input frame.
     */
    public function clearPreviousLines(): void
    {
        $this->frameRenderer->clearHistoryLines();
    }

    /**
     * Set whether the next readline() call should continue the current frame.
     */
    public function setContinueFrame(bool $continue): void
    {
        $this->continueFrame = $continue;
    }

    /**
     * Store the number of newlines written by SubmitLineAction to escape the frame.
     */
    public function setLastSubmitEscapeRows(int $rows): void
    {
        $this->lastSubmitEscapeRows = $rows;
    }

    /**
     * Escape below the current frame before aborting back to Shell.php.
     *
     * Shell.php writes the final newline after readline returns false or a
     * BreakException bubbles up, so only escape the remaining frame rows here.
     */
    public function escapeCurrentFrameForAbort(Buffer $buffer): void
    {
        $lineCount = \substr_count($buffer->getText(), "\n") + 1;
        $remainingInputLines = $lineCount - $buffer->getCurrentLineNumber();
        $escapeRows = \max(0, $remainingInputLines + $this->getInputFrameOuterRowCount() - 1);

        if ($escapeRows > 0) {
            $this->terminal->write(\str_repeat("\n", $escapeRows));
        }
    }

    /**
     * Get the history.
     */
    public function getHistory(): History
    {
        return $this->history;
    }

    /**
     * Enable or disable inline suggestions.
     */
    public function setUseSuggestions(bool $enabled): void
    {
        $this->useSuggestions = $enabled;
    }

    /**
     * Enable or disable syntax highlighting.
     */
    public function setUseSyntaxHighlighting(bool $enabled): void
    {
        $this->frameRenderer->setUseSyntaxHighlighting($enabled);
    }

    /**
     * Get the suggestion engine.
     */
    public function getSuggestionEngine(): SuggestionEngine
    {
        return $this->suggestionEngine;
    }

    /**
     * Get the current suggestion.
     */
    public function getCurrentSuggestion(): ?SuggestionResult
    {
        return $this->currentSuggestion;
    }

    /**
     * Clear the current suggestion.
     */
    public function clearSuggestion(): void
    {
        $this->currentSuggestion = null;
        $this->suggestionEngine->clearCache();
    }

    /**
     * Update suggestion based on current buffer state.
     */
    private function updateSuggestion(Buffer $buffer): void
    {
        if (!$this->useSuggestions) {
            return;
        }

        if ($this->activeMode() !== null || $this->multilineMode) {
            $this->clearSuggestion();

            return;
        }

        $this->currentSuggestion = $this->suggestionEngine->getSuggestion(
            $buffer->getText(),
            $buffer->getCursor()
        );
    }

    /**
     * Get the history search state machine.
     */
    public function getSearch(): HistorySearch
    {
        return $this->search;
    }

    /**
     * Push a mode onto the stack. The mode becomes the active key/render handler.
     */
    public function pushMode(ReadlineMode $mode): void
    {
        $this->modeStack[] = $mode;
    }

    /**
     * Pop the top mode off the stack and call its onExit. Also clears any
     * overlay the mode left behind so the next render starts fresh. Returns
     * the popped mode, or null if the stack was already empty.
     */
    public function popMode(): ?ReadlineMode
    {
        $mode = \array_pop($this->modeStack);
        if ($mode !== null) {
            $mode->onExit();
            $this->frameRenderer->setOverlay(null);
        }

        return $mode;
    }

    /**
     * The current active mode (top of stack), or null if no mode is active.
     */
    public function activeMode(): ?ReadlineMode
    {
        return \end($this->modeStack) ?: null;
    }

    /**
     * Construct a Pager that shares this Readline's terminal/input/frame
     * collaborators. Caller supplies the InteractiveSession because raw-mode
     * lifecycle is owned at a higher layer.
     */
    public function createPager(InteractiveSession $session): Pager
    {
        return new Pager($this->terminal, $session, $this->inputQueue, $this->frameRenderer);
    }
}
