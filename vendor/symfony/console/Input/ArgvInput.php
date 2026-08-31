<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Input;

use Symfony\Component\Console\Exception\RuntimeException;

/**
 * ArgvInput represents an input coming from the CLI arguments.
 *
 * Usage:
 *
 *     $input = new ArgvInput();
 *
 * By default, the `$_SERVER['argv']` array is used for the input values.
 *
 * This can be overridden by explicitly passing the input values in the constructor:
 *
 *     $input = new ArgvInput($_SERVER['argv']);
 *
 * If you pass it yourself, don't forget that the first element of the array
 * is the name of the running application.
 *
 * When passing an argument to the constructor, be sure that it respects
 * the same rules as the argv one. It's almost always better to use the
 * `StringInput` when you want to provide your own input.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @see http://www.gnu.org/software/libc/manual/html_node/Argument-Syntax.html
 * @see http://www.opengroup.org/onlinepubs/009695399/basedefs/xbd_chap12.html#tag_12_02
 */
class ArgvInput extends Input
{
    /** @var list<string> */
    private array $tokens;
    private array $parsed;

    /** @param list<string>|null $argv */
    public function __construct(?array $argv = null, ?InputDefinition $definition = null)
    {
        $argv ??= $_SERVER['argv'] ?? [];

        foreach ($argv as $arg) {
            if (!\is_scalar($arg) && !$arg instanceof \Stringable) {
                throw new RuntimeException(\sprintf('Argument values expected to be all scalars, got "%s".', get_debug_type($arg)));
            }
        }

        // strip the application name
        array_shift($argv);

        $this->tokens = $argv;

        parent::__construct($definition);
    }

    /** @param list<string> $tokens */
    protected function setTokens(array $tokens): void
    {
        $this->tokens = $tokens;
    }

    protected function parse(): void
    {
        $parseOptions = true;
        $this->parsed = $this->tokens;
        while (null !== $token = array_shift($this->parsed)) {
            $parseOptions = $this->parseToken($token, $parseOptions);
        }
    }

    protected function parseToken(string $token, bool $parseOptions): bool
    {
        if ($parseOptions && '' == $token) {
            $this->parseArgument($token);
        } elseif ($parseOptions && '--' == $token) {
            return false;
        } elseif ($parseOptions && str_starts_with($token, '--')) {
            $this->parseLongOption($token);
        } elseif ($parseOptions && '-' === $token[0] && '-' !== $token) {
            $this->parseShortOption($token);
        } else {
            $this->parseArgument($token);
        }

        return $parseOptions;
    }

    /**
     * Parses a short option.
     */
    private function parseShortOption(string $token): void
    {
        $name = substr($token, 1);

        if (\strlen($name) > 1) {
            if ($this->definition->hasShortcut($name[0]) && $this->definition->getOptionForShortcut($name[0])->acceptValue()) {
                // an option with a value (with no space)
                $this->addShortOption($name[0], substr($name, 1));
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
     * @throws RuntimeException When option given doesn't exist
     */
    private function parseShortOptionSet(string $name): void
    {
        $len = \strlen($name);
        for ($i = 0; $i < $len; ++$i) {
            if (!$this->definition->hasShortcut($name[$i])) {
                $encoding = mb_detect_encoding($name, null, true);
                throw new RuntimeException(\sprintf('The "-%s" option does not exist.', false === $encoding ? $name[$i] : mb_substr($name, $i, 1, $encoding)));
            }

            $option = $this->definition->getOptionForShortcut($name[$i]);
            if ($option->acceptValue()) {
                $this->addLongOption($option->getName(), $i === $len - 1 ? null : substr($name, $i + 1));

                break;
            }

            $this->addLongOption($option->getName(), null);
        }
    }

    /**
     * Parses a long option.
     */
    private function parseLongOption(string $token): void
    {
        $name = substr($token, 2);

        if (false !== $pos = strpos($name, '=')) {
            if ('' === $value = substr($name, $pos + 1)) {
                array_unshift($this->parsed, $value);
            }
            $this->addLongOption(substr($name, 0, $pos), $value);
        } else {
            $this->addLongOption($name, null);
        }
    }

    /**
     * Parses an argument.
     *
     * @throws RuntimeException When too many arguments are given
     */
    private function parseArgument(string $token): void
    {
        $c = \count($this->arguments);

        // if input is expecting another argument, add it
        if ($this->definition->hasArgument($c)) {
            $arg = $this->definition->getArgument($c);
            $this->arguments[$arg->getName()] = $arg->isArray() ? [$token] : $token;

        // if last argument isArray(), append token to last argument
        } elseif ($this->definition->hasArgument($c - 1) && $this->definition->getArgument($c - 1)->isArray()) {
            $arg = $this->definition->getArgument($c - 1);
            $this->arguments[$arg->getName()][] = $token;

        // unexpected argument
        } else {
            $all = $this->definition->getArguments();
            $symfonyCommandName = null;
            if (($inputArgument = $all[$key = array_key_first($all) ?? ''] ?? null) && 'command' === $inputArgument->getName()) {
                $symfonyCommandName = $this->arguments['command'] ?? null;
                unset($all[$key]);
            }

            if (\count($all)) {
                if ($symfonyCommandName) {
                    $message = \sprintf('Too many arguments to "%s" command, expected arguments "%s".', $symfonyCommandName, implode('" "', array_keys($all)));
                } else {
                    $message = \sprintf('Too many arguments, expected arguments "%s".', implode('" "', array_keys($all)));
                }
            } elseif ($symfonyCommandName) {
                $message = \sprintf('No arguments expected for "%s" command, got "%s".', $symfonyCommandName, $token);
            } else {
                $message = \sprintf('No arguments expected, got "%s".', $token);
            }

            throw new RuntimeException($message);
        }
    }

    /**
     * Adds a short option value.
     *
     * @throws RuntimeException When option given doesn't exist
     */
    private function addShortOption(string $shortcut, mixed $value): void
    {
        if (!$this->definition->hasShortcut($shortcut)) {
            throw new RuntimeException(\sprintf('The "-%s" option does not exist.', $shortcut));
        }

        $this->addLongOption($this->definition->getOptionForShortcut($shortcut)->getName(), $value);
    }

    /**
     * Adds a long option value.
     *
     * @throws RuntimeException When option given doesn't exist
     */
    private function addLongOption(string $name, mixed $value): void
    {
        if (!$this->definition->hasOption($name)) {
            if (!$this->definition->hasNegation($name)) {
                throw new RuntimeException(\sprintf('The "--%s" option does not exist.', $name));
            }

            $optionName = $this->definition->negationToName($name);
            if (null !== $value) {
                throw new RuntimeException(\sprintf('The "--%s" option does not accept a value.', $name));
            }
            $this->options[$optionName] = false;

            return;
        }

        $option = $this->definition->getOption($name);

        if (null !== $value && !$option->acceptValue()) {
            throw new RuntimeException(\sprintf('The "--%s" option does not accept a value.', $name));
        }

        if (\in_array($value, ['', null], true) && $option->acceptValue() && \count($this->parsed)) {
            // if option accepts an optional or mandatory argument
            // let's see if there is one provided
            $next = array_shift($this->parsed);
            if ((isset($next[0]) && '-' !== $next[0]) || \in_array($next, ['', null], true)) {
                $value = $next;
            } else {
                array_unshift($this->parsed, $next);
            }
        }

        if (null === $value) {
            if ($option->isValueRequired()) {
                throw new RuntimeException(\sprintf('The "--%s" option requires a value.', $name));
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

    public function getFirstArgument(): ?string
    {
        $isOption = false;
        foreach ($this->tokens as $i => $token) {
            if ($token && '-' === $token[0]) {
                if (str_contains($token, '=') || !isset($this->tokens[$i + 1])) {
                    continue;
                }

                // If it's a long option, consider that everything after "--" is the option name.
                // Otherwise, use the last char (if it's a short option set, only the last one can take a value with space separator)
                $name = '-' === $token[1] ? substr($token, 2) : substr($token, -1);
                if (!isset($this->options[$name]) && !$this->definition->hasShortcut($name)) {
                    // noop
                } elseif ((isset($this->options[$name]) || isset($this->options[$name = $this->definition->shortcutToName($name)])) && $this->tokens[$i + 1] === $this->options[$name]) {
                    $isOption = true;
                }

                continue;
            }

            if ($isOption) {
                $isOption = false;
                continue;
            }

            return $token;
        }

        return null;
    }

    public function hasParameterOption(string|array $values, bool $onlyParams = false): bool
    {
        $values = (array) $values;

        foreach ($this->tokens as $token) {
            if ($onlyParams && '--' === $token) {
                return false;
            }
            foreach ($values as $value) {
                // Options with values:
                //   For long options, test for '--option=' at beginning
                //   For short options, test for '-o' at beginning
                $leading = str_starts_with($value, '--') ? $value.'=' : $value;
                if ($token === $value || '' !== $leading && str_starts_with($token, $leading)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getParameterOption(string|array $values, string|bool|int|float|array|null $default = false, bool $onlyParams = false): mixed
    {
        $values = (array) $values;
        $tokens = $this->tokens;

        while (0 < \count($tokens)) {
            $token = array_shift($tokens);
            if ($onlyParams && '--' === $token) {
                return $default;
            }

            foreach ($values as $value) {
                if ($token === $value) {
                    return array_shift($tokens);
                }
                // Options with values:
                //   For long options, test for '--option=' at beginning
                //   For short options, test for '-o' at beginning
                $leading = str_starts_with($value, '--') ? $value.'=' : $value;
                if ('' !== $leading && str_starts_with($token, $leading)) {
                    return substr($token, \strlen($leading));
                }
            }
        }

        return $default;
    }

    /**
     * Returns un-parsed and not validated tokens.
     *
     * @param bool $strip Whether to return the raw parameters (false) or the values after the command name (true)
     *
     * @return list<string>
     */
    public function getRawTokens(bool $strip = false): array
    {
        if (!$strip) {
            return $this->tokens;
        }

        $parameters = [];
        $keep = false;
        foreach ($this->tokens as $value) {
            if (!$keep && $value === $this->getFirstArgument()) {
                $keep = true;

                continue;
            }
            if ($keep) {
                $parameters[] = $value;
            }
        }

        return $parameters;
    }

    /**
     * Returns a stringified representation of the args passed to the command.
     */
    public function __toString(): string
    {
        $tokens = array_map(function ($token) {
            if (preg_match('{^(-[^=]+=)(.+)}', $token, $match)) {
                return $match[1].$this->escapeToken($match[2]);
            }

            if ($token && '-' !== $token[0]) {
                return $this->escapeToken($token);
            }

            return $token;
        }, $this->tokens);

        return implode(' ', $tokens);
    }
}
