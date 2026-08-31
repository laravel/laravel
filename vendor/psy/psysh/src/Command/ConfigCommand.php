<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command;

use Psy\Command\Config\AbstractConfigCommand;
use Psy\Command\Config\ConfigGetCommand;
use Psy\Command\Config\ConfigListCommand;
use Psy\Command\Config\ConfigSetCommand;
use Psy\CommandArgumentCompletionAware;
use Psy\Completion\AnalysisResult;
use Psy\Completion\FuzzyMatcher;
use Psy\Input\CodeArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Inspect and update runtime-configurable settings for the current shell session.
 */
class ConfigCommand extends AbstractConfigCommand implements CommandArgumentCompletionAware
{
    private const ACTIONS = ['list', 'get', 'set'];

    /** @var array{supported: bool, completions: string[]}|null */
    private ?array $lastCompletionResult = null;
    private string $lastCompletionInput = '';

    private string $defaultHelp = '';

    protected function configure(): void
    {
        $this->defaultHelp = \implode("\n", [
            'Inspect or update runtime-configurable PsySH settings for the current session.',
            '',
            'e.g.',
            '<return>>>> config list</return>',
            '<return>>>> config get verbosity</return>',
            '<return>>>> config set verbosity debug</return>',
            '<return>>>> config set pager off</return>',
            '<return>>>> config set clipboardCommand auto</return>',
            '',
            'Runtime-configurable keys include '.$this->formatOptionNames([
                'verbosity',
                'useUnicode',
                'errorLoggingLevel',
                'clipboardCommand',
                'useOsc52Clipboard',
                'colorMode',
                'theme',
                'pager',
                'requireSemicolons',
                'semicolonsSuppressReturn',
                'useBracketedPaste',
                'useSyntaxHighlighting',
                'useSuggestions',
            ]).'.',
        ]);

        $this
            ->setName('config')
            ->setDefinition([
                new InputArgument('action', InputArgument::OPTIONAL, 'Action: list, get, or set.', 'list'),
                new InputArgument('key', InputArgument::OPTIONAL, 'Runtime-configurable option to inspect or update.'),
                new CodeArgument('value', CodeArgument::OPTIONAL, 'New value when using `set`.'),
            ])
            ->setDescription('Inspect or update runtime-configurable PsySH settings for the current session.')
            ->setHelp($this->defaultHelp);
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        if ($input->hasParameterOption(['--help', '-h'], true)) {
            $output->writeln($this->asTextForInput($input));

            return 0;
        }

        return parent::run($input, $output);
    }

    public function asTextForInput(InputInterface $input): string
    {
        $action = $this->getActionFromInput($input);

        if ($action === '') {
            return $this->asText();
        }

        $command = $this->createChildCommand($action);

        if ($command === null) {
            return $this->asText();
        }

        return $command->asTextForInput($this->createChildInput($command, $action, $this->rawArguments($input)));
    }

    public function getArgumentCompletions(AnalysisResult $analysis): array
    {
        return $this->resolveArgumentCompletion($analysis)['completions'];
    }

    public function supportsArgumentCompletion(AnalysisResult $analysis): bool
    {
        return $this->resolveArgumentCompletion($analysis)['supported'];
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = \strtolower((string) $input->getArgument('action'));
        $command = $this->createChildCommand($action);

        if ($command === null) {
            throw new \InvalidArgumentException(\sprintf('Unknown config action: %s. Expected list, get, or set.', $action));
        }

        return $command->run($this->createChildInput($command, $action, [
            $action,
            (string) $input->getArgument('key'),
            (string) $input->getArgument('value'),
        ]), $output);
    }

    /**
     * @param string[] $arguments
     */
    private function createChildInput(Command $command, string $action, array $arguments): ArrayInput
    {
        $parameters = [];

        switch ($action) {
            case 'get':
                if (isset($arguments[1]) && $arguments[1] !== '') {
                    $parameters['key'] = $arguments[1];
                }
                break;

            case 'set':
                if (isset($arguments[1]) && $arguments[1] !== '') {
                    $parameters['key'] = $arguments[1];
                }
                if (isset($arguments[2]) && $arguments[2] !== '') {
                    $parameters['value'] = $arguments[2];
                }
                break;
        }

        $input = new ArrayInput($parameters, $command->getDefinition());
        $input->setInteractive(false);

        return $input;
    }

    private function createChildCommand(string $action): ?Command
    {
        switch ($action) {
            case '':
            case 'list':
                $command = new ConfigListCommand();
                break;

            case 'get':
                $command = new ConfigGetCommand();
                break;

            case 'set':
                $command = new ConfigSetCommand();
                break;

            default:
                return null;
        }

        $command->setConfiguration($this->getConfig());
        $command->setApplication($this->getApplication());

        return $command;
    }

    private function getActionFromInput(InputInterface $input): string
    {
        $arguments = $this->rawArguments($input);

        return \strtolower($arguments[0] ?? '');
    }

    /**
     * Extract positional arguments from the raw input string.
     *
     * Symfony's Input classes don't expose raw tokens after parsing, so we
     * re-tokenize __toString() output to recover them for child command routing.
     *
     * @return string[]
     */
    private function rawArguments(InputInterface $input): array
    {
        if (!$input instanceof ArrayInput && !$input instanceof StringInput) {
            return [];
        }

        return $this->tokenizeArguments($input->__toString());
    }

    /**
     * @return array{0: string[], 1: bool}
     */
    private function parseCompletionInput(string $input): array
    {
        $trimmed = \rtrim($input);

        return [$this->tokenizeArguments($trimmed), $trimmed !== $input];
    }

    /**
     * Tokenize an input string into positional arguments, skipping options.
     *
     * @return string[]
     */
    private function tokenizeArguments(string $input): array
    {
        if ($input === '') {
            return [];
        }

        \preg_match_all('/"[^"]*"|\'[^\']*\'|\S+/', $input, $matches);

        $arguments = [];

        foreach ($matches[0] as $token) {
            if ($token === '--') {
                break;
            }

            if ($token !== '' && $token[0] === '-') {
                continue;
            }

            $arguments[] = $this->trimQuotes($token);
        }

        $first = $arguments[0] ?? null;
        if ($first === $this->getName() || \in_array($first, $this->getAliases(), true)) {
            \array_shift($arguments);
        }

        return $arguments;
    }

    /**
     * @param string[] $arguments
     */
    private function isCompletingSetValue(array $arguments, bool $hasTrailingSpace): bool
    {
        $count = \count($arguments);

        if ($count < 2 || $count > 3) {
            return false;
        }

        return ($count === 2 && $hasTrailingSpace) || ($count === 3 && !$hasTrailingSpace);
    }

    /**
     * @return array{supported: bool, completions: string[]}
     */
    private function resolveArgumentCompletion(AnalysisResult $analysis): array
    {
        if ($this->lastCompletionResult !== null && $this->lastCompletionInput === $analysis->input) {
            return $this->lastCompletionResult;
        }

        $this->lastCompletionInput = $analysis->input;

        return $this->lastCompletionResult = $this->doResolveArgumentCompletion($analysis->input);
    }

    /**
     * @return array{supported: bool, completions: string[]}
     */
    private function doResolveArgumentCompletion(string $input): array
    {
        [$arguments, $hasTrailingSpace] = $this->parseCompletionInput($input);
        $count = \count($arguments);
        $action = \strtolower($arguments[0] ?? '');

        if ($count === 0 || ($count === 1 && !$hasTrailingSpace)) {
            return ['supported' => true, 'completions' => self::ACTIONS];
        }

        switch ($action) {
            case 'list':
                return ['supported' => true, 'completions' => []];

            case 'get':
            case 'set':
                // Completing the key name (cursor on or just after argument position 2)
                if ($count <= 2 && ($count === 1 || !$hasTrailingSpace)) {
                    return ['supported' => true, 'completions' => $this->getOptionNames()];
                }

                if ($action !== 'set') {
                    return ['supported' => true, 'completions' => []];
                }

                if (!$this->isCompletingSetValue($arguments, $hasTrailingSpace)) {
                    return ['supported' => true, 'completions' => []];
                }

                return $this->resolveSetValueCompletion($arguments, $hasTrailingSpace);

            default:
                return ['supported' => true, 'completions' => self::ACTIONS];
        }
    }

    /**
     * @param string[] $arguments
     *
     * @return array{supported: bool, completions: string[]}
     */
    private function resolveSetValueCompletion(array $arguments, bool $hasTrailingSpace): array
    {
        $key = $arguments[1];
        $option = $this->getOption($key);
        if ($option === null) {
            return ['supported' => false, 'completions' => []];
        }

        $acceptsFreeForm = false;
        $completions = [];
        foreach ($option['acceptedValues'] as $value) {
            if ($value !== '' && $value[0] === '<') {
                $acceptsFreeForm = true;
            } else {
                $completions[] = $value;
            }
        }

        if (!$acceptsFreeForm) {
            return ['supported' => $completions !== [], 'completions' => $completions];
        }

        $valuePrefix = $hasTrailingSpace ? '' : ($arguments[2] ?? '');

        if ($valuePrefix === '') {
            return ['supported' => $completions !== [], 'completions' => $completions];
        }

        if (FuzzyMatcher::filter($valuePrefix, $completions) !== []) {
            return ['supported' => true, 'completions' => $completions];
        }

        return ['supported' => false, 'completions' => []];
    }

    private function trimQuotes(string $token): string
    {
        $quote = $token[0] ?? '';

        if (($quote === '"' || $quote === '\'') && \substr($token, -1) === $quote) {
            return \substr($token, 1, -1);
        }

        return $token;
    }
}
