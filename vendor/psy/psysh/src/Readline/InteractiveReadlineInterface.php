<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline;

use Psy\Completion\CompletionEngine;
use Psy\Output\Theme;
use Psy\Readline\Interactive\Input\History;
use Psy\Readline\Interactive\Pager;
use Psy\Readline\Interactive\Terminal;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface for interactive readline implementations with additional features.
 *
 * This interface extends the shell readline interface with rich terminal
 * behavior such as themed prompts, output-aware redisplay, advanced
 * completion, and interactive history integration.
 */
interface InteractiveReadlineInterface extends ShellReadlineInterface
{
    /**
     * Set the theme (currently used for prompt configuration).
     */
    public function setTheme(Theme $theme): void;

    /**
     * Enable or disable bracketed paste mode.
     */
    public function setUseBracketedPaste(bool $enabled): void;

    /**
     * Enable or disable inline suggestions.
     */
    public function setUseSuggestions(bool $enabled): void;

    /**
     * Enable or disable syntax highlighting.
     */
    public function setUseSyntaxHighlighting(bool $enabled): void;

    /**
     * Set the CompletionEngine for context-aware tab completion and autosuggestions.
     */
    public function setCompletionEngine(CompletionEngine $completionEngine): void;

    /**
     * Set the output stream.
     */
    public function setOutput(OutputInterface $output, ?Terminal $terminal = null): void;

    /**
     * Get the history instance.
     */
    public function getHistory(): History;

    /**
     * List entries added during the current REPL session.
     *
     * @return string[]
     */
    public function listSessionHistory(): array;

    /**
     * Report whether visible output was written since the last input.
     *
     * The readline implementation uses this to decide whether to continue
     * the current input frame or start a fresh one.
     */
    public function setOutputWritten(bool $written): void;

    /**
     * Get the userland Pager that shares this readline's terminal, input
     * queue, and renderer.
     */
    public function getPager(): Pager;
}
