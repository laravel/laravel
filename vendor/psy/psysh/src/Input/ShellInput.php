<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Input;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\StringInput;

/**
 * A StringInput subclass specialized for code arguments.
 */
class ShellInput extends StringInput
{
    public const REGEX_STRING = '([^\s]+?)(?:\s|(?<!\\\\)"|(?<!\\\\)\'|$)';

    private bool $hasCodeArgument = false;

    /**
     * Unlike the parent implementation's tokens, this contains an array of
     * token/rest pairs, so that code arguments can be handled while parsing.
     */
    private array $tokenPairs;
    private array $parsed = [];

    /**
     * Constructor.
     *
     * @param string $input An array of parameters from the CLI (in the argv format)
     */
    public function __construct(string $input)
    {
        parent::__construct($input);

        $this->tokenPairs = $this->tokenize($input);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException if $definition has CodeArgument before the final argument position
     */
    public function bind(InputDefinition $definition): void
    {
        $hasCodeArgument = false;

        if ($definition->getArgumentCount() > 0) {
            $args = $definition->getArguments();
            $lastArg = \array_pop($args);
            foreach ($args as $arg) {
                if ($arg instanceof CodeArgument) {
                    $msg = \sprintf('Unexpected CodeArgument before the final position: %s', $arg->getName());
                    throw new \InvalidArgumentException($msg);
                }
            }

            if ($lastArg instanceof CodeArgument) {
                $hasCodeArgument = true;
            }
        }

        $this->hasCodeArgument = $hasCodeArgument;

        parent::bind($definition);
    }

    /**
     * Tokenizes a string.
     *
     * The version of this on StringInput is good, but doesn't handle code
     * arguments if they're at all complicated. This does :)
     *
     * @param string $input The input to tokenize
     *
     * @return array An array of token/rest pairs
     *
     * @throws \InvalidArgumentException When unable to parse input (should never happen)
     */
    private function tokenize(string $input): array
    {
        $tokens = [];
        $length = \strlen($input);
        $cursor = 0;
        while ($cursor < $length) {
            if (\preg_match('/\s+/A', $input, $match, 0, $cursor)) {
            } elseif (\preg_match('/([^="\'\s]+?)(=?)('.StringInput::REGEX_QUOTED_STRING.'+)/A', $input, $match, 0, $cursor)) {
                $tokens[] = [
                    $match[1].$match[2].\stripcslashes(\str_replace(['"\'', '\'"', '\'\'', '""'], '', \substr($match[3], 1, \strlen($match[3]) - 2))),
                    \stripcslashes(\substr($input, $cursor)),
                ];
            } elseif (\preg_match('/'.StringInput::REGEX_QUOTED_STRING.'/A', $input, $match, 0, $cursor)) {
                $tokens[] = [
                    \stripcslashes(\substr($match[0], 1, \strlen($match[0]) - 2)),
                    \stripcslashes(\substr($input, $cursor)),
                ];
            } elseif (\preg_match('/'.self::REGEX_STRING.'/A', $input, $match, 0, $cursor)) {
                $tokens[] = [
                    \stripcslashes($match[1]),
                    \stripcslashes(\substr($input, $cursor)),
                ];
            } else {
                // should never happen
                // @codeCoverageIgnoreStart
                throw new \InvalidArgumentException(\sprintf('Unable to parse input near "... %s ..."', \substr($input, $cursor, 10)));
                // @codeCoverageIgnoreEnd
            }

            $cursor += \strlen($match[0]);
        }

        return $tokens;
    }

    /**
     * Same as parent, but with some bonus handling for code arguments.
     */
    protected function parse(): void
    {
        $parseOptions = true;
        $this->parsed = $this->tokenPairs;
        while (null !== $tokenPair = \array_shift($this->parsed)) {
            // token is what you'd expect. rest is the remainder of the input
            // string, including token, and will be used if this is a code arg.
            list($token, $rest) = $tokenPair;

            if ($parseOptions && '' === $token) {
                $this->parseShellArgument($token, $rest);
            } elseif ($parseOptions && '--' === $token) {
                $parseOptions = false;
            } elseif ($parseOptions && 0 === \strpos($token, '--')) {
                $this->parseLongOption($token);
            } elseif ($parseOptions && '-' === $token[0] && '-' !== $token) {
                $this->parseShortOption($token);
            } else {
                $this->parseShellArgument($token, $rest);
            }
        }
    }

    /**
     * Parses an argument, with bonus handling for code arguments.
     *
     * @param string $token The current token
     * @param string $rest  The remaining unparsed input, including the current token
     *
     * @throws \RuntimeException When too many arguments are given
     */
    private function parseShellArgument(string $token, string $rest)
    {
        $c = \count($this->arguments);

        // if input is expecting another argument, add it
        if ($this->definition->hasArgument($c)) {
            $arg = $this->definition->getArgument($c);

            if ($arg instanceof CodeArgument) {
                // When we find a code argument, we're done parsing. Add the
                // remaining input to the current argument and call it a day.
                $this->parsed = [];
                $this->arguments[$arg->getName()] = $rest;
            } else {
                $this->arguments[$arg->getName()] = $arg->isArray() ? [$token] : $token;
            }

            return;
        }

        // (copypasta)
        //
        // @codeCoverageIgnoreStart

        // if last argument isArray(), append token to last argument
        if ($this->definition->hasArgument($c - 1) && $this->definition->getArgument($c - 1)->isArray()) {
            $arg = $this->definition->getArgument($c - 1);
            $this->arguments[$arg->getName()][] = $token;

            return;
        }

        // unexpected argument
        $all = $this->definition->getArguments();
        if (\count($all)) {
            throw new \RuntimeException(\sprintf('Too many arguments, expected arguments "%s".', \implode('" "', \array_keys($all))));
        }

        throw new \RuntimeException(\sprintf('No arguments expected, got "%s".', $token));
        // @codeCoverageIgnoreEnd
    }

    // Everything below this is copypasta from ArgvInput private methods
    // @codeCoverageIgnoreStart

    /**
     * Parses a short option.
     *
     * @param string $token The current token
     */
    private function parseShortOption(string $token)
    {
        $name = \substr($token, 1);

        if (\strlen($name) > 1) {
            if ($this->definition->hasShortcut($name[0]) && $this->definition->getOptionForShortcut($name[0])->acceptValue()) {
                // an option with a value (with no space)
                $this->addShortOption($name[0], \substr($name, 1));
            } else {
                $this->parseShortOptionSet($name);
            }
        } else {
            $this->addShortOption($name, null);
        }
    }

    /**
     * Parses a short option set.
     *
     * @param string $name The current token
     *
     * @throws \RuntimeException When option given doesn't exist
     */
    private function parseShortOptionSet(string $name)
    {
        $len = \strlen($name);
        for ($i = 0; $i < $len; $i++) {
            if (!$this->definition->hasShortcut($name[$i])) {
                throw new \RuntimeException(\sprintf('The "-%s" option does not exist.', $name[$i]));
            }

            $option = $this->definition->getOptionForShortcut($name[$i]);
            if ($option->acceptValue()) {
                $this->addLongOption($option->getName(), $i === $len - 1 ? null : \substr($name, $i + 1));

                break;
            } else {
                $this->addLongOption($option->getName(), null);
            }
        }
    }

    /**
     * Parses a long option.
     *
     * @param string $token The current token
     */
    private function parseLongOption(string $token)
    {
        $name = \substr($token, 2);

        if (false !== $pos = \strpos($name, '=')) {
            if (($value = \substr($name, $pos + 1)) === '') {
                \array_unshift($this->parsed, [$value, null]);
            }
            $this->addLongOption(\substr($name, 0, $pos), $value);
        } else {
            $this->addLongOption($name, null);
        }
    }

    /**
     * Adds a short option value.
     *
     * @param string $shortcut The short option key
     * @param mixed  $value    The value for the option
     *
     * @throws \RuntimeException When option given doesn't exist
     */
    private function addShortOption(string $shortcut, $value)
    {
        if (!$this->definition->hasShortcut($shortcut)) {
            throw new \RuntimeException(\sprintf('The "-%s" option does not exist.', $shortcut));
        }

        $this->addLongOption($this->definition->getOptionForShortcut($shortcut)->getName(), $value);
    }

    /**
     * Adds a long option value.
     *
     * @param string $name  The long option key
     * @param mixed  $value The value for the option
     *
     * @throws \RuntimeException When option given doesn't exist
     */
    private function addLongOption(string $name, $value)
    {
        if (!$this->definition->hasOption($name)) {
            throw new \RuntimeException(\sprintf('The "--%s" option does not exist.', $name));
        }

        $option = $this->definition->getOption($name);

        if (null !== $value && !$option->acceptValue()) {
            throw new \RuntimeException(\sprintf('The "--%s" option does not accept a value.', $name));
        }

        if (\in_array($value, ['', null], true) && $option->acceptValue() && \count($this->parsed)) {
            // if option accepts an optional or mandatory argument
            // let's see if there is one provided
            $next = \array_shift($this->parsed);
            $nextToken = $next[0];
            if ((isset($nextToken[0]) && '-' !== $nextToken[0]) || \in_array($nextToken, ['', null], true)) {
                $value = $nextToken;
            } else {
                \array_unshift($this->parsed, $next);
            }
        }

        if ($value === null) {
            if ($option->isValueRequired()) {
                throw new \RuntimeException(\sprintf('The "--%s" option requires a value.', $name));
            }

            if (!$option->isArray() && !$option->isValueOptional()) {
                $value = true;
            }
        }

        if ($option->isArray()) {
            $this->options[$name][] = $value;
        } else {
            $this->options[$name] = $value;
        }
    }

    // @codeCoverageIgnoreEnd
}
