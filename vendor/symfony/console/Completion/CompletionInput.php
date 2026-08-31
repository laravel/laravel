<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Completion;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * An input specialized for shell completion.
 *
 * This input allows unfinished option names or values and exposes what kind of
 * completion is expected.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
final class CompletionInput extends ArgvInput
{
    public const TYPE_ARGUMENT_VALUE = 'argument_value';
    public const TYPE_OPTION_VALUE = 'option_value';
    public const TYPE_OPTION_NAME = 'option_name';
    public const TYPE_NONE = 'none';

    private array $tokens;
    private int $currentIndex;
    private string $completionType;
    private ?string $completionName = null;
    private string $completionValue = '';

    /**
     * Converts a terminal string into tokens.
     *
     * This is required for shell completions without COMP_WORDS support.
     */
    public static function fromString(string $inputStr, int $currentIndex): self
    {
        preg_match_all('/(?<=^|\s)([\'"]?)(.+?)(?<!\\\\)\1(?=$|\s)/', $inputStr, $tokens);

        return self::fromTokens($tokens[0], $currentIndex);
    }

    /**
     * Create an input based on an COMP_WORDS token list.
     *
     * @param string[] $tokens       the set of split tokens (e.g. COMP_WORDS or argv)
     * @param int      $currentIndex the index of the cursor (e.g. COMP_CWORD)
     */
    public static function fromTokens(array $tokens, int $currentIndex): self
    {
        $input = new self($tokens);
        $input->tokens = $tokens;
        $input->currentIndex = $currentIndex;

        return $input;
    }

    public function bind(InputDefinition $definition): void
    {
        parent::bind($definition);

        $relevantToken = $this->getRelevantToken();
        if ('-' === $relevantToken[0]) {
            // the current token is an input option: complete either option name or option value
            [$optionToken, $optionValue] = explode('=', $relevantToken, 2) + ['', ''];

            $option = $this->getOptionFromToken($optionToken);
            if (null === $option && !$this->isCursorFree()) {
                $this->completionType = self::TYPE_OPTION_NAME;
                $this->completionValue = $relevantToken;

                return;
            }

            if ($option?->acceptValue()) {
                $this->completionType = self::TYPE_OPTION_VALUE;
                $this->completionName = $option->getName();
                $this->completionValue = $optionValue ?: (!str_starts_with($optionToken, '--') ? substr($optionToken, 2) : '');

                return;
            }
        }

        $previousToken = $this->tokens[$this->currentIndex - 1];
        if ('-' === $previousToken[0] && '' !== trim($previousToken, '-')) {
            // check if previous option accepted a value
            $previousOption = $this->getOptionFromToken($previousToken);
            if ($previousOption?->acceptValue()) {
                $this->completionType = self::TYPE_OPTION_VALUE;
                $this->completionName = $previousOption->getName();
                $this->completionValue = $relevantToken;

                return;
            }
        }

        // complete argument value
        $this->completionType = self::TYPE_ARGUMENT_VALUE;

        foreach ($this->definition->getArguments() as $argumentName => $argument) {
            if (!isset($this->arguments[$argumentName])) {
                break;
            }

            $argumentValue = $this->arguments[$argumentName];
            $this->completionName = $argumentName;
            if (\is_array($argumentValue)) {
                $this->completionValue = $argumentValue ? $argumentValue[array_key_last($argumentValue)] : null;
            } else {
                $this->completionValue = $argumentValue;
            }
        }

        if ($this->currentIndex >= \count($this->tokens)) {
            if (!isset($this->arguments[$argumentName]) || $this->definition->getArgument($argumentName)->isArray()) {
                $this->completionName = $argumentName;
            } else {
                // we've reached the end
                $this->completionType = self::TYPE_NONE;
                $this->completionName = null;
            }

            $this->completionValue = '';
        }
    }

    /**
     * Returns the type of completion required.
     *
     * TYPE_ARGUMENT_VALUE when completing the value of an input argument
     * TYPE_OPTION_VALUE   when completing the value of an input option
     * TYPE_OPTION_NAME    when completing the name of an input option
     * TYPE_NONE           when nothing should be completed
     *
     * TYPE_OPTION_NAME and TYPE_NONE are already implemented by the Console component.
     *
     * @return self::TYPE_*
     */
    public function getCompletionType(): string
    {
        return $this->completionType;
    }

    /**
     * The name of the input option or argument when completing a value.
     *
     * @return string|null returns null when completing an option name
     */
    public function getCompletionName(): ?string
    {
        return $this->completionName;
    }

    /**
     * The value already typed by the user (or empty string).
     */
    public function getCompletionValue(): string
    {
        return $this->completionValue;
    }

    public function mustSuggestOptionValuesFor(string $optionName): bool
    {
        return self::TYPE_OPTION_VALUE === $this->getCompletionType() && $optionName === $this->getCompletionName();
    }

    public function mustSuggestArgumentValuesFor(string $argumentName): bool
    {
        return self::TYPE_ARGUMENT_VALUE === $this->getCompletionType() && $argumentName === $this->getCompletionName();
    }

    protected function parseToken(string $token, bool $parseOptions): bool
    {
        try {
            return parent::parseToken($token, $parseOptions);
        } catch (RuntimeException) {
            // suppress errors, completed input is almost never valid
        }

        return $parseOptions;
    }

    private function getOptionFromToken(string $optionToken): ?InputOption
    {
        $optionName = ltrim($optionToken, '-');
        if (!$optionName) {
            return null;
        }

        if ('-' === ($optionToken[1] ?? ' ')) {
            // long option name
            return $this->definition->hasOption($optionName) ? $this->definition->getOption($optionName) : null;
        }

        // short option name
        return $this->definition->hasShortcut($optionName[0]) ? $this->definition->getOptionForShortcut($optionName[0]) : null;
    }

    /**
     * The token of the cursor, or the last token if the cursor is at the end of the input.
     */
    private function getRelevantToken(): string
    {
        return $this->tokens[$this->isCursorFree() ? $this->currentIndex - 1 : $this->currentIndex];
    }

    /**
     * Whether the cursor is "free" (i.e. at the end of the input preceded by a space).
     */
    private function isCursorFree(): bool
    {
        $nrOfTokens = \count($this->tokens);
        if ($this->currentIndex > $nrOfTokens) {
            throw new \LogicException('Current index is invalid, it must be the number of input tokens or one more.');
        }

        return $this->currentIndex >= $nrOfTokens;
    }

    public function __toString(): string
    {
        $str = '';
        foreach ($this->tokens as $i => $token) {
            $str .= $token;

            if ($this->currentIndex === $i) {
                $str .= '|';
            }

            $str .= ' ';
        }

        if ($this->currentIndex > $i) {
            $str .= '|';
        }

        return rtrim($str);
    }
}
