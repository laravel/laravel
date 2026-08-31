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

use Psy\CodeAnalysis\BufferAnalyzer;
use Psy\Readline\Interactive\Input\StatementCompletenessPolicy;
use Psy\Shell;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Decorates a legacy physical-line readline and returns logical statements.
 */
class LegacyReadline implements ShellReadlineInterface
{
    private Readline $readline;
    private ?Shell $shell = null;
    private ?OutputInterface $output = null;
    private bool $requireSemicolons = false;
    private ?string $bufferPrompt = null;
    private BufferAnalyzer $bufferAnalyzer;
    private StatementCompletenessPolicy $statementCompletenessPolicy;
    private array $buffer = [];

    /**
     * @param mixed $readline A decorated readline instance
     */
    public function __construct($readline = null, $historySize = 0, $eraseDups = false)
    {
        if (!($readline instanceof Readline)) {
            throw new \InvalidArgumentException('LegacyReadline requires a decorated Readline instance.');
        }

        $this->readline = $readline;
        $this->bufferAnalyzer = new BufferAnalyzer();
        $this->statementCompletenessPolicy = new StatementCompletenessPolicy($this->bufferAnalyzer);
    }

    public static function isSupported(): bool
    {
        return true;
    }

    public static function supportsBracketedPaste(): bool
    {
        return false;
    }

    public function addHistory(string $line): bool
    {
        return $this->readline->addHistory($line);
    }

    public function clearHistory(): bool
    {
        return $this->readline->clearHistory();
    }

    public function listHistory(): array
    {
        return $this->readline->listHistory();
    }

    public function readHistory(): bool
    {
        return $this->readline->readHistory();
    }

    public function readline(?string $prompt = null)
    {
        $lines = $this->buffer;
        if ($lines !== []) {
            $text = \implode("\n", $lines);
            if ($this->statementCompletenessPolicy->isCompleteStatement($text)) {
                $this->clearBuffer();

                return $text;
            }
        }

        while (true) {
            $linePrompt = ($lines === []) ? $prompt : ($this->bufferPrompt ?? $prompt);
            $line = $this->readline->readline($linePrompt);
            if ($line === false) {
                if ($lines === []) {
                    return false;
                }

                // Mirror the legacy shell behavior for Ctrl+D mid-buffer:
                // clear the partial statement and return to a fresh prompt.
                $this->clearBuffer();
                if ($this->output !== null) {
                    $this->output->writeln('');
                }

                return '';
            }

            if ($lines !== [] && $this->isCommand($line) && !$this->inputInOpenStringOrComment($line)) {
                return $line;
            }

            [$line, $keepBufferOpen] = $this->normalizeLine($line);
            $lines[] = $line;
            $this->buffer = $lines;
            $text = \implode("\n", $lines);

            if (!$keepBufferOpen && $this->statementCompletenessPolicy->isCompleteStatement($text)) {
                $this->clearBuffer();

                return $text;
            }
        }
    }

    public function redisplay()
    {
        $this->readline->redisplay();
    }

    public function writeHistory(): bool
    {
        return $this->readline->writeHistory();
    }

    public function setRequireSemicolons(bool $require): void
    {
        $this->requireSemicolons = $require;
        $this->statementCompletenessPolicy = new StatementCompletenessPolicy(
            $this->bufferAnalyzer,
            $this->requireSemicolons
        );
    }

    public function setBufferPrompt(?string $prompt): void
    {
        $this->bufferPrompt = $prompt;
    }

    /**
     * Set the shell output for buffer-clearing notifications.
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * Set the shell instance for command detection while buffering input.
     */
    public function setShell(Shell $shell): void
    {
        $this->shell = $shell;
    }

    /**
     * Get the buffered physical lines for the current incomplete statement.
     *
     * @return string[]
     */
    public function getBuffer(): array
    {
        return $this->buffer;
    }

    /**
     * Check whether there is buffered multiline input.
     */
    public function hasBuffer(): bool
    {
        return $this->buffer !== [];
    }

    /**
     * Append generated code to the active multiline buffer.
     */
    public function append(string $code): void
    {
        foreach (\explode("\n", $code) as $line) {
            $this->buffer[] = $line;
        }
    }

    /**
     * Clear the current incomplete statement buffer.
     */
    public function clearBuffer(): void
    {
        $this->buffer = [];
    }

    /**
     * Strip the legacy trailing backslash continuation marker.
     */
    private function normalizeLine(string $line): array
    {
        $trimmed = \rtrim($line);

        if (\substr($trimmed, -1) === '\\') {
            return [\substr($trimmed, 0, -1), true];
        }

        return [$line, false];
    }

    /**
     * Check if the current physical line is a PsySH command.
     */
    private function isCommand(string $input): bool
    {
        if ($this->shell === null) {
            return false;
        }

        return $this->shell->hasCommand($input);
    }

    /**
     * Check whether the current buffer plus input is in an open string or comment.
     */
    private function inputInOpenStringOrComment(string $input): bool
    {
        if ($this->buffer === []) {
            return false;
        }

        $code = $this->buffer;
        $code[] = $input;

        return $this->bufferAnalyzer->analyze(\implode("\n", $code))->endsInOpenStringOrComment();
    }
}
