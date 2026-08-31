<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Actions;

use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter\Standard as Printer;
use Psy\Input\CodeArgument;
use Psy\Input\ShellInput;
use Psy\ParserFactory;
use Psy\Readline\Interactive\Helper\ArgumentExtractorVisitor;
use Psy\Readline\Interactive\Input\Buffer;
use Psy\Readline\Interactive\Input\History;
use Psy\Readline\Interactive\Readline;
use Psy\Readline\Interactive\Terminal;
use Psy\Shell;

/**
 * History expansion action.
 *
 * Expands history patterns when Tab is pressed:
 * - !! = previous command
 * - !$ = last argument of previous command
 * - !^ = first argument of previous command
 * - !* = all arguments of previous command
 */
class HistoryExpansionAction implements ActionInterface
{
    private History $history;
    private ?Shell $shell;

    public function __construct(History $history, ?Shell $shell = null)
    {
        $this->history = $history;
        $this->shell = $shell;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        $text = $buffer->getText();
        $cursor = $buffer->getCursor();

        $expansion = $this->detectExpansion($text, $cursor);

        if ($expansion === null) {
            return true;
        }

        $replacement = $this->getExpansion($expansion['pattern']);

        if ($replacement === null) {
            $terminal->bell();

            return true;
        }

        $before = \mb_substr($text, 0, $expansion['start']);
        $after = \mb_substr($text, $expansion['end']);
        $newText = $before.$replacement.$after;

        $buffer->setText($newText);
        $buffer->setCursor($expansion['start'] + \mb_strlen($replacement));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'history-expansion';
    }

    /**
     * Detect expansion pattern at or before cursor.
     *
     * @return array|null Array with 'pattern', 'start', 'end' or null
     */
    public function detectExpansion(string $text, int $cursor): ?array
    {
        $patterns = ['!!', '!*', '!^', '!$'];

        foreach ($patterns as $pattern) {
            $len = \mb_strlen($pattern);
            $start = $cursor - $len;

            if ($start < 0) {
                continue;
            }

            $candidate = \mb_substr($text, $start, $len);

            if ($candidate === $pattern) {
                // Ensure pattern is at a word boundary
                if ($start > 0) {
                    $prevChar = \mb_substr($text, $start - 1, 1);
                    if (\preg_match('/[\w$]/u', $prevChar)) {
                        continue;
                    }
                }

                return [
                    'pattern' => $pattern,
                    'start'   => $start,
                    'end'     => $cursor,
                ];
            }
        }

        return null;
    }

    /**
     * Get expansion for a pattern.
     *
     * @return string|null Expansion text or null if not available
     */
    private function getExpansion(string $pattern): ?string
    {
        $entries = $this->history->getAll();
        if (empty($entries)) {
            return null;
        }

        $lastCommand = $entries[\count($entries) - 1]['command'];

        switch ($pattern) {
            case '!!':
                return $lastCommand;

            case '!$':
                return $this->getLastArgument($lastCommand);

            case '!^':
                return $this->getFirstArgument($lastCommand);

            case '!*':
                return $this->getAllArguments($lastCommand);

            default:
                return null;
        }
    }

    /**
     * Get the first argument (index 0 is the command itself).
     */
    private function getFirstArgument(string $command): ?string
    {
        return $this->parseArguments($command)[1] ?? null;
    }

    /**
     * Get the last argument.
     */
    private function getLastArgument(string $command): ?string
    {
        $args = $this->parseArguments($command);

        if (empty($args)) {
            return null;
        }

        return \end($args);
    }

    /**
     * Get all arguments (excluding the command) joined with spaces.
     */
    private function getAllArguments(string $command): ?string
    {
        $args = \array_slice($this->parseArguments($command), 1);

        return empty($args) ? null : \implode(' ', $args);
    }

    /**
     * Parse a command string into arguments.
     *
     * Handles both PsySH commands (like `ls $var`, `show Class::method`) and
     * PHP code (like `echo $foo, $bar`).
     *
     * @return string[] Array of arguments
     */
    private function parseArguments(string $command): array
    {
        $command = \trim($command);

        if ($command === '') {
            return [];
        }

        $psyshArgs = $this->parsePsyshCommand($command);
        if ($psyshArgs !== null) {
            return $psyshArgs;
        }

        return $this->parsePhpCode($command);
    }

    /**
     * Parse PsySH command to extract code arguments.
     *
     * Returns null if the command is not a PsySH command with a CodeArgument.
     *
     * Examples:
     * - `ls $var` → ['ls', '$var']
     * - `ls -al $foo->bar()` → ['ls', '$foo->bar()']
     * - `show --all SomeClass` → ['show', 'SomeClass']
     *
     * @return string[]|null Array with command and code argument, or null if not a PsySH command
     */
    private function parsePsyshCommand(string $command): ?array
    {
        if ($this->shell === null) {
            return null;
        }

        if (!\preg_match('/^([^\s]+)/', $command, $match)) {
            return null;
        }

        $commandName = $match[1];

        try {
            $cmd = $this->shell->get($commandName);
        } catch (\Throwable $e) {
            return null;
        }

        $definition = $cmd->getDefinition();

        // Only handle commands that accept a CodeArgument
        $hasCodeArg = false;
        foreach ($definition->getArguments() as $arg) {
            if ($arg instanceof CodeArgument) {
                $hasCodeArg = true;
                break;
            }
        }

        if (!$hasCodeArg) {
            return null;
        }

        $remainder = \ltrim(\substr($command, \strlen($commandName)));

        if ($remainder === '') {
            return [$commandName];
        }

        try {
            $input = new ShellInput($remainder);
            $input->bind($definition);

            foreach ($definition->getArguments() as $arg) {
                if ($arg instanceof CodeArgument) {
                    $codeArg = $input->getArgument($arg->getName());
                    if ($codeArg !== null && $codeArg !== '') {
                        return [$commandName, $codeArg];
                    }
                    break;
                }
            }

            return [$commandName];
        } catch (\Throwable $e) {
            return $this->parseArgumentsFallback($command);
        }
    }

    /**
     * Parse PHP code to extract function/method call arguments.
     *
     * Uses PHP Parser to extract arguments from the AST.
     *
     * @return string[] Array of arguments
     */
    private function parsePhpCode(string $code): array
    {
        try {
            $parser = (new ParserFactory())->createParser();
            $printer = new Printer();
            $phpCode = '<?php '.$code;

            // Suppress warnings so the parser throws on invalid code
            // rather than recovering with a partial (incorrect) AST.
            $stmts = @$parser->parse($phpCode.';');
            if ($stmts === null) {
                $stmts = @$parser->parse($phpCode);
            }

            if ($stmts === null || empty($stmts)) {
                return $this->parseArgumentsFallback($code);
            }

            $visitor = new ArgumentExtractorVisitor($printer);
            $traverser = new NodeTraverser();
            $traverser->addVisitor($visitor);
            $traverser->traverse($stmts);

            $result = $visitor->getArguments();

            // Fall back for things like "ls -al" that parse as PHP but aren't function calls
            if (empty($result)) {
                return $this->parseArgumentsFallback($code);
            }

            return $result;
        } catch (\Throwable $e) {
            return $this->parseArgumentsFallback($code);
        }
    }

    /**
     * Fallback argument parser that splits on whitespace while preserving quotes.
     *
     * @return string[] Array of arguments
     */
    private function parseArgumentsFallback(string $command): array
    {
        $args = [];
        $current = '';
        $inQuote = null;
        $len = \strlen($command);

        for ($i = 0; $i < $len; $i++) {
            $char = $command[$i];

            if ($inQuote !== null) {
                $current .= $char;
                if ($char === $inQuote && ($i === 0 || $command[$i - 1] !== '\\')) {
                    $inQuote = null;
                }
            } elseif ($char === '"' || $char === "'") {
                $inQuote = $char;
                $current .= $char;
            } elseif (\ctype_space($char)) {
                if ($current !== '') {
                    $args[] = $current;
                    $current = '';
                }
            } else {
                $current .= $char;
            }
        }

        if ($current !== '') {
            $args[] = $current;
        }

        return $args;
    }
}
