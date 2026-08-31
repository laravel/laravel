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

use Psy\CommandAware;
use Psy\Completion\CompletionEngine;
use Psy\Exception\ThrowUpException;
use Psy\Output\Theme;
use Psy\Readline\Interactive\Helper\DebugLog;
use Psy\Readline\Interactive\Input\History as InteractiveHistory;
use Psy\Readline\Interactive\Input\StdinReader;
use Psy\Readline\Interactive\InteractiveSession;
use Psy\Readline\Interactive\Pager;
use Psy\Readline\Interactive\Readline as InternalReadline;
use Psy\Readline\Interactive\Suggestion\Source\ContextAwareSource;
use Psy\Readline\Interactive\Terminal;
use Psy\Readline\Interactive\TerminalOutput;
use Psy\Shell;
use Psy\ShellAware;
use Psy\Util\TerminalColor;
use Psy\Util\Tty;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Interactive readline implementation.
 *
 * A pure-PHP readline with visual feedback, autosuggestions, tab completion,
 * and other interactive features.
 */
class InteractiveReadline implements InteractiveReadlineInterface, ShellAware, CommandAware
{
    private InternalReadline $readline;
    private InteractiveHistory $history;
    private Terminal $terminal;
    private InteractiveSession $session;
    private ?Pager $pager = null;
    /** @var string|false */
    private $historyFile;
    /** @var string|false */
    private $historyImportFile = false;
    private bool $historyFilesResolved = false;
    private int $historySize;
    private bool $eraseDups;

    /**
     * Interactive readline is supported if stdin is a TTY.
     *
     * Interactive readline requires an interactive terminal because it uses
     * stty commands for raw mode. When stdin is piped, stty will fail with
     * "stdin isn't a terminal" warnings.
     */
    public static function isSupported(): bool
    {
        // Requires Symfony Console 5.1+ for Cursor support
        if (!\class_exists('Symfony\Component\Console\Cursor')) {
            return false;
        }

        return Tty::supportsStty();
    }

    /**
     * Interactive readline supports bracketed paste.
     */
    public static function supportsBracketedPaste(): bool
    {
        return true;
    }

    private bool $booted = false;

    /**
     * Create an interactive readline instance.
     *
     * @param string|false|null $historyFile
     * @param int               $historySize
     * @param bool              $eraseDups
     */
    public function __construct($historyFile = null, $historySize = 0, $eraseDups = false)
    {
        $this->historyFile = ($historyFile !== null) ? $historyFile : false;
        $this->historySize = $historySize;
        $this->eraseDups = $eraseDups;
    }

    /**
     * {@inheritdoc}
     */
    public function setOutput(OutputInterface $output, ?Terminal $terminal = null): void
    {
        if (!($output instanceof StreamOutput)) {
            throw new \InvalidArgumentException('InteractiveReadline requires a StreamOutput instance.');
        }

        DebugLog::enable($output->getVerbosity());

        $this->terminal = $terminal ?? new Terminal(new StdinReader(\STDIN), new TerminalOutput($output));
        $this->session = new InteractiveSession($this->terminal);
        $this->history = new InteractiveHistory($this->historySize, $this->eraseDups);
        $this->resolveHistoryFiles();
        $this->loadHistory();
        $this->readline = new InternalReadline($this->terminal, null, $this->history);

        $this->applyDynamicInputFrameColor($output);

        $this->booted = true;

        if (DebugLog::isEnabled()) {
            $this->showDebugInfo();
        }
    }

    /**
     * Ensure setOutput has been called before using readline internals.
     *
     * @throws \RuntimeException if readline has not been booted via setOutput
     */
    private function assertBooted(): void
    {
        if (!$this->booted) {
            throw new \RuntimeException('InteractiveReadline has not been booted. Call setOutput() first.');
        }
    }

    /**
     * Show debug logging information (whisper message).
     */
    private function showDebugInfo(): void
    {
        $logPath = DebugLog::getLogPath();
        DebugLog::separator('INTERACTIVE READLINE SESSION START');
        DebugLog::log('System', 'INITIALIZED', ['log_path' => $logPath]);

        // Output whisper message to stderr so it doesn't interfere with shell output
        $message = \sprintf(
            "\033[90mDebug logging enabled: tail -f %s\033[0m\n",
            $logPath
        );
        \fwrite(\STDERR, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function addHistory(string $line): bool
    {
        $this->assertBooted();
        $this->history->add($line);
        $this->writeHistory();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clearHistory(): bool
    {
        $this->assertBooted();
        $this->history->clear();
        $this->writeHistory();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function listHistory(): array
    {
        $this->assertBooted();
        $entries = $this->history->getAll();

        // Extract just the command strings
        return \array_map(fn ($entry) => $entry['command'], $entries);
    }

    /**
     * {@inheritdoc}
     */
    public function listSessionHistory(): array
    {
        $this->assertBooted();

        return $this->history->getSessionCommands();
    }

    /**
     * {@inheritdoc}
     */
    public function readHistory(): bool
    {
        $this->assertBooted();
        $this->resolveHistoryFiles();
        if (!$this->hasReadableHistory()) {
            return false;
        }

        $this->loadHistory();

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Note: $prompt is unused here, as multi-line buffer rendering is managed internally
     */
    public function readline(?string $prompt = null)
    {
        $this->assertBooted();
        try {
            $this->session->start();
        } catch (\RuntimeException $e) {
            throw new ThrowUpException($e);
        }

        return $this->readline->readline();
    }

    /**
     * {@inheritdoc}
     */
    public function setTheme(Theme $theme): void
    {
        $this->assertBooted();
        $this->readline->setTheme($theme);
    }

    /**
     * {@inheritdoc}
     */
    public function redisplay()
    {
        // Interactive readline doesn't expose redisplay (it's handled by the readline itself)
    }

    /**
     * {@inheritdoc}
     */
    public function writeHistory(): bool
    {
        $this->assertBooted();
        $this->resolveHistoryFiles();
        if ($this->historyFile !== false) {
            $this->history->saveToFile($this->historyFile);
        }

        return true;
    }

    /**
     * Get the userland Pager, lazily constructed using this Readline's
     * Terminal/InputQueue/FrameRenderer and InteractiveSession.
     *
     * Available only after setOutput() has been called.
     */
    public function getPager(): Pager
    {
        $this->assertBooted();
        if ($this->pager === null) {
            $this->pager = $this->readline->createPager($this->session);
        }

        return $this->pager;
    }

    /**
     * {@inheritdoc}
     */
    public function setShell(Shell $shell): void
    {
        $this->assertBooted();
        $this->readline->setShell($shell);
    }

    public function setCommands(array $commands): void
    {
        $this->assertBooted();
        $this->readline->getCommandHighlighter()->setCommands($commands);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequireSemicolons(bool $require): void
    {
        $this->assertBooted();
        $this->readline->setRequireSemicolons($require);
    }

    /**
     * {@inheritdoc}
     */
    public function setCompletionEngine(CompletionEngine $completionEngine): void
    {
        $this->assertBooted();
        $this->readline->setCompletionEngine($completionEngine);

        $suggestionSource = new ContextAwareSource($completionEngine);
        $this->readline->getSuggestionEngine()->addSource($suggestionSource);
    }

    /**
     * {@inheritdoc}
     */
    public function setOutputWritten(bool $written): void
    {
        $this->readline->setContinueFrame(!$written);
    }

    /**
     * {@inheritdoc}
     */
    public function setUseSuggestions(bool $enabled): void
    {
        $this->assertBooted();
        $this->readline->setUseSuggestions($enabled);
    }

    /**
     * {@inheritdoc}
     */
    public function setUseSyntaxHighlighting(bool $enabled): void
    {
        $this->assertBooted();
        $this->readline->setUseSyntaxHighlighting($enabled);
    }

    /**
     * {@inheritdoc}
     */
    public function setUseBracketedPaste(bool $enabled): void
    {
        $this->assertBooted();
        $this->session->setUseBracketedPaste($enabled);
    }

    /**
     * Enable or disable Unicode in PsySH-owned terminal UI.
     */
    public function setUseUnicode(bool $enabled): void
    {
        $this->assertBooted();
        $this->terminal->setUseUnicode($enabled);
    }

    /**
     * {@inheritdoc}
     */
    public function getHistory(): InteractiveHistory
    {
        $this->assertBooted();

        return $this->history;
    }

    /**
     * Query the terminal's background color and override the input_frame style.
     *
     * When detection succeeds, the background is a subtle tint over the
     * terminal's actual background, which looks better than the static fallback
     * across both dark and light themes.
     */
    private function applyDynamicInputFrameColor(StreamOutput $output): void
    {
        $formatter = $output->getFormatter();
        if (!$formatter->isDecorated()) {
            return;
        }

        $this->setDynamicStyle($formatter, 'input_frame', TerminalColor::computeInputFrameBackground());
        $this->setDynamicStyle($formatter, 'input_frame_error', TerminalColor::computeInputFrameErrorBackground());
    }

    /**
     * Set a dynamic formatter style from a computed hex color.
     *
     * Hex colors require Symfony Console 5.2+; falls back gracefully.
     */
    private function setDynamicStyle($formatter, string $styleName, ?string $bgHex): void
    {
        if ($bgHex === null) {
            return;
        }

        try {
            $formatter->setStyle($styleName, new OutputFormatterStyle(null, $bgHex));
        } catch (\InvalidArgumentException $e) {
            // Keep the existing static style
        }
    }

    /**
     * Resolve history read/write files.
     *
     * Legacy plain-text history files are import-only: when one is configured,
     * new history is written to a sibling JSONL file.
     */
    private function resolveHistoryFiles(): void
    {
        if ($this->historyFilesResolved || $this->historyFile === false) {
            return;
        }

        $this->historyFilesResolved = true;
        if (!$this->isLegacyHistoryFile($this->historyFile)) {
            return;
        }

        $legacyHistoryFile = $this->historyFile;
        $jsonlHistoryFile = $legacyHistoryFile.'.jsonl';

        if (\file_exists($jsonlHistoryFile)) {
            $this->historyFile = $jsonlHistoryFile;

            return;
        }

        $this->historyImportFile = $legacyHistoryFile;
        $this->historyFile = $jsonlHistoryFile;
    }

    /**
     * Load history from the configured JSONL file, or import from legacy file.
     */
    private function loadHistory(): void
    {
        if ($this->historyFile !== false && \file_exists($this->historyFile)) {
            $this->history->loadFromFile($this->historyFile);

            return;
        }

        if ($this->historyImportFile !== false && \file_exists($this->historyImportFile)) {
            $this->history->importFromFile($this->historyImportFile);
        }
    }

    /**
     * Check whether either the primary history file or import source exists.
     */
    private function hasReadableHistory(): bool
    {
        if ($this->historyFile !== false && \file_exists($this->historyFile)) {
            return true;
        }

        return $this->historyImportFile !== false && \file_exists($this->historyImportFile);
    }

    /**
     * Check whether a history file is in legacy plain-text readline format.
     *
     * Peeks at the first non-empty line: if it's a valid JSON history line
     * (signature or entry), the file is not legacy; otherwise it is.
     */
    private function isLegacyHistoryFile(string $path): bool
    {
        if (!\file_exists($path) || !\is_readable($path)) {
            return false;
        }

        $handle = @\fopen($path, 'r');
        if ($handle === false) {
            return false;
        }

        while (($line = \fgets($handle)) !== false) {
            if (\trim($line) === '') {
                continue;
            }

            @\fclose($handle);

            return !InteractiveHistory::isJsonHistoryLine($line);
        }

        @\fclose($handle);

        return false;
    }
}
