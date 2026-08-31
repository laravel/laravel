<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Helper;

use Psy\CommandAware;
use Psy\Formatter\CodeFormatter;
use Psy\Input\CodeArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * Highlights PsySH commands using command metadata from Symfony definitions.
 */
class CommandHighlighter implements CommandAware
{
    public const STYLE_COMMAND = 'command';
    public const STYLE_OPTION = 'command_option';
    public const STYLE_ARGUMENT = 'command_argument';

    /** @var array<string, Command> */
    private array $commandMap = [];

    /** @var array<string, array<string, InputOption>> */
    private array $shortcutMapCache = [];

    /**
     * {@inheritdoc}
     */
    public function setCommands(array $commands): void
    {
        $this->commandMap = [];

        foreach ($commands as $command) {
            $this->commandMap[$command->getName()] = $command;

            foreach ($command->getAliases() as $alias) {
                $this->commandMap[$alias] = $command;
            }
        }

        $this->shortcutMapCache = [];
    }

    /**
     * Check whether the given name is a known command.
     */
    public function hasCommand(string $name): bool
    {
        return isset($this->commandMap[$name]);
    }

    /**
     * Highlight a complete command string.
     */
    public function highlight(string $text, OutputFormatterInterface $formatter): string
    {
        return \implode("\n", $this->highlightLines($text, $formatter));
    }

    /**
     * Highlight a command string into ANSI-safe lines.
     *
     * @return string[]
     */
    public function highlightLines(string $text, OutputFormatterInterface $formatter): array
    {
        if ($text === '') {
            return [''];
        }

        if (!\preg_match('/^(\s*)(\S+)(.*)$/s', $text, $matches)) {
            return [$text];
        }

        $lines = [$matches[1].$this->applyStyle($matches[2], self::STYLE_COMMAND, $formatter)];
        $command = $this->resolveCommand($matches[2]);

        if ($command === null) {
            $this->appendText($lines, $matches[3]);

            return $lines;
        }

        $definition = $command->getDefinition();
        $shortcuts = $this->getCachedShortcutMap($matches[2], $definition);
        $arguments = \array_values($definition->getArguments());
        $argumentIndex = 0;
        $expectingOptionValue = null;
        $endOfOptions = false;

        foreach ($this->tokenize($matches[3]) as $segment) {
            if ($segment['type'] === 'whitespace') {
                $this->appendText($lines, $segment['text']);
                continue;
            }

            $token = $segment['text'];

            if ($expectingOptionValue instanceof InputOption) {
                $this->appendText($lines, $this->highlightValue($token, $formatter));
                $expectingOptionValue = null;
                continue;
            }

            if (!$endOfOptions && $token === '--') {
                $this->appendText($lines, $this->applyStyle($token, self::STYLE_OPTION, $formatter));
                $endOfOptions = true;
                continue;
            }

            if (!$endOfOptions) {
                $longOption = $this->matchLongOption($token, $definition, $formatter);
                if ($longOption !== null) {
                    $this->appendText($lines, $longOption['text']);
                    $expectingOptionValue = $longOption['nextExpectsValue'];
                    continue;
                }

                $shortOption = $this->matchShortOption($token, $shortcuts, $formatter);
                if ($shortOption !== null) {
                    $this->appendText($lines, $shortOption['text']);
                    $expectingOptionValue = $shortOption['nextExpectsValue'];
                    continue;
                }
            }

            $argument = $arguments[$argumentIndex] ?? null;

            if ($argument instanceof CodeArgument) {
                $this->appendLines($lines, CodeFormatter::formatInputLines((string) \substr($matches[3], $segment['offset']), $formatter));

                return $lines;
            }

            $this->appendText($lines, $this->highlightValue($token, $formatter));

            if ($argument instanceof InputArgument && !$argument->isArray()) {
                $argumentIndex++;
            }
        }

        return $lines;
    }

    private function resolveCommand(string $name): ?Command
    {
        return $this->commandMap[$name] ?? null;
    }

    /**
     * @return array<string, InputOption>
     */
    private function getCachedShortcutMap(string $commandName, InputDefinition $definition): array
    {
        if (isset($this->shortcutMapCache[$commandName])) {
            return $this->shortcutMapCache[$commandName];
        }

        $shortcuts = [];

        foreach ($definition->getOptions() as $option) {
            if (!$option->getShortcut()) {
                continue;
            }

            foreach (\explode('|', $option->getShortcut()) as $shortcut) {
                $shortcuts[$shortcut] = $option;
            }
        }

        return $this->shortcutMapCache[$commandName] = $shortcuts;
    }

    /**
     * @return array{text: string, nextExpectsValue: InputOption|null}|null
     */
    private function matchLongOption(string $token, InputDefinition $definition, OutputFormatterInterface $formatter): ?array
    {
        if (!\preg_match('/^(--[A-Za-z0-9][A-Za-z0-9-]*)(?:=(.*))?$/s', $token, $matches)) {
            return null;
        }

        $name = \substr($matches[1], 2);
        if (!$definition->hasOption($name)) {
            return [
                'text'              => $this->applyStyle($token, self::STYLE_OPTION, $formatter),
                'nextExpectsValue'  => null,
            ];
        }

        $option = $definition->getOption($name);
        $text = $this->applyStyle($matches[1], self::STYLE_OPTION, $formatter);

        if (\array_key_exists(2, $matches) && $matches[2] !== '') {
            return [
                'text'              => $text.'='.$this->highlightValue($matches[2], $formatter),
                'nextExpectsValue'  => null,
            ];
        }

        return [
            'text'              => $text,
            'nextExpectsValue'  => $option->isValueRequired() ? $option : null,
        ];
    }

    /**
     * @param array<string, InputOption> $shortcuts
     *
     * @return array{text: string, nextExpectsValue: InputOption|null}|null
     */
    private function matchShortOption(string $token, array $shortcuts, OutputFormatterInterface $formatter): ?array
    {
        if (!\preg_match('/^-[^-].*$/', $token)) {
            return null;
        }

        $cluster = \substr($token, 1);
        $consumed = '';

        for ($i = 0, $length = \strlen($cluster); $i < $length; $i++) {
            $shortcut = $cluster[$i];
            if (!isset($shortcuts[$shortcut])) {
                return [
                    'text'              => $this->applyStyle($token, self::STYLE_OPTION, $formatter),
                    'nextExpectsValue'  => null,
                ];
            }

            $consumed .= $shortcut;
            $option = $shortcuts[$shortcut];
            if (!$option->acceptValue()) {
                continue;
            }

            $optionText = '-'.$consumed;
            $valueText = \substr($cluster, $i + 1);

            if ($valueText !== '') {
                return [
                    'text'              => $this->applyStyle($optionText, self::STYLE_OPTION, $formatter).$this->highlightValue($valueText, $formatter),
                    'nextExpectsValue'  => null,
                ];
            }

            return [
                'text'              => $this->applyStyle($optionText, self::STYLE_OPTION, $formatter),
                'nextExpectsValue'  => $option->isValueRequired() ? $option : null,
            ];
        }

        return [
            'text'              => $this->applyStyle($token, self::STYLE_OPTION, $formatter),
            'nextExpectsValue'  => null,
        ];
    }

    private function highlightValue(string $token, OutputFormatterInterface $formatter): string
    {
        if (\preg_match('/^[+-]?\d+(?:\.\d+)?$/', $token)) {
            return $this->applyStyle($token, CodeFormatter::HIGHLIGHT_NUMBER, $formatter);
        }

        return $this->applyStyle($token, self::STYLE_ARGUMENT, $formatter);
    }

    private function applyStyle(string $text, string $style, OutputFormatterInterface $formatter): string
    {
        if (!$formatter->isDecorated() || !$formatter->hasStyle($style)) {
            return $text;
        }

        return $formatter->getStyle($style)->apply($text);
    }

    /**
     * @return array<int, array{type: string, text: string, offset: int}>
     */
    private function tokenize(string $text): array
    {
        $tokens = [];
        $length = \strlen($text);
        $offset = 0;

        while ($offset < $length) {
            $char = $text[$offset];

            if (\ctype_space($char)) {
                $start = $offset;
                while ($offset < $length && \ctype_space($text[$offset])) {
                    $offset++;
                }

                $tokens[] = [
                    'type'   => 'whitespace',
                    'text'   => \substr($text, $start, $offset - $start),
                    'offset' => $start,
                ];

                continue;
            }

            $start = $offset;
            $quote = null;

            while ($offset < $length) {
                $char = $text[$offset];

                if ($quote !== null) {
                    $offset++;
                    if ($char === $quote && ($offset < 2 || $text[$offset - 2] !== '\\')) {
                        $quote = null;
                    }
                    continue;
                }

                if ($char === '"' || $char === "'") {
                    $quote = $char;
                    $offset++;
                    continue;
                }

                if (\ctype_space($char)) {
                    break;
                }

                $offset++;
            }

            $tokens[] = [
                'type'   => 'token',
                'text'   => \substr($text, $start, $offset - $start),
                'offset' => $start,
            ];
        }

        return $tokens;
    }

    /**
     * @param string[] $lines
     */
    private function appendText(array &$lines, string $text): void
    {
        $parts = \preg_split('/\r\n?|\n/', $text);
        if ($parts === false || $parts === []) {
            return;
        }

        $lines[\count($lines) - 1] .= \array_shift($parts);

        foreach ($parts as $part) {
            $lines[] = $part;
        }
    }

    /**
     * @param string[] $lines
     * @param string[] $extraLines
     */
    private function appendLines(array &$lines, array $extraLines): void
    {
        if ($extraLines === []) {
            return;
        }

        $lines[\count($lines) - 1] .= \array_shift($extraLines);
        \array_push($lines, ...$extraLines);
    }
}
