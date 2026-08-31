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

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\InvalidOptionException;

/**
 * ArrayInput represents an input provided as an array.
 *
 * Usage:
 *
 *     $input = new ArrayInput(['command' => 'foo:bar', 'foo' => 'bar', '--bar' => 'foobar']);
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ArrayInput extends Input
{
    public function __construct(
        private array $parameters,
        ?InputDefinition $definition = null,
    ) {
        parent::__construct($definition);
    }

    public function getFirstArgument(): ?string
    {
        foreach ($this->parameters as $param => $value) {
            if ($param && \is_string($param) && '-' === $param[0]) {
                continue;
            }

            return $value;
        }

        return null;
    }

    public function hasParameterOption(string|array $values, bool $onlyParams = false): bool
    {
        $values = (array) $values;

        foreach ($this->parameters as $k => $v) {
            if (!\is_int($k)) {
                $v = $k;
            }

            if ($onlyParams && '--' === $v) {
                return false;
            }

            if (\in_array($v, $values)) {
                return true;
            }
        }

        return false;
    }

    public function getParameterOption(string|array $values, string|bool|int|float|array|null $default = false, bool $onlyParams = false): mixed
    {
        $values = (array) $values;

        foreach ($this->parameters as $k => $v) {
            if ($onlyParams && ('--' === $k || (\is_int($k) && '--' === $v))) {
                return $default;
            }

            if (\is_int($k)) {
                if (\in_array($v, $values)) {
                    return true;
                }
            } elseif (\in_array($k, $values)) {
                return $v;
            }
        }

        return $default;
    }

    /**
     * Returns a stringified representation of the args passed to the command.
     */
    public function __toString(): string
    {
        $params = [];
        foreach ($this->parameters as $param => $val) {
            if ($param && \is_string($param) && '-' === $param[0]) {
                $glue = ('-' === $param[1]) ? '=' : ' ';
                if (\is_array($val)) {
                    foreach ($val as $v) {
                        $params[] = $param.('' != $v ? $glue.$this->escapeToken($v) : '');
                    }
                } else {
                    $params[] = $param.('' != $val ? $glue.$this->escapeToken($val) : '');
                }
            } else {
                $params[] = \is_array($val) ? implode(' ', array_map($this->escapeToken(...), $val)) : $this->escapeToken($val);
            }
        }

        return implode(' ', $params);
    }

    protected function parse(): void
    {
        foreach ($this->parameters as $key => $value) {
            if ('--' === $key) {
                return;
            }
            if (str_starts_with($key, '--')) {
                $this->addLongOption(substr($key, 2), $value);
            } elseif (str_starts_with($key, '-')) {
                $this->addShortOption(substr($key, 1), $value);
            } else {
                $this->addArgument($key, $value);
            }
        }
    }

    /**
     * Adds a short option value.
     *
     * @throws InvalidOptionException When option given doesn't exist
     */
    private function addShortOption(string $shortcut, mixed $value): void
    {
        if (!$this->definition->hasShortcut($shortcut)) {
            throw new InvalidOptionException(\sprintf('The "-%s" option does not exist.', $shortcut));
        }

        $this->addLongOption($this->definition->getOptionForShortcut($shortcut)->getName(), $value);
    }

    /**
     * Adds a long option value.
     *
     * @throws InvalidOptionException When option given doesn't exist
     * @throws InvalidOptionException When a required value is missing
     */
    private function addLongOption(string $name, mixed $value): void
    {
        if (!$this->definition->hasOption($name)) {
            if (!$this->definition->hasNegation($name)) {
                throw new InvalidOptionException(\sprintf('The "--%s" option does not exist.', $name));
            }

            $optionName = $this->definition->negationToName($name);
            $this->options[$optionName] = false;

            return;
        }

        $option = $this->definition->getOption($name);

        if (null === $value) {
            if ($option->isValueRequired()) {
                throw new InvalidOptionException(\sprintf('The "--%s" option requires a value.', $name));
            }

            if (!$option->isValueOptional()) {
                $value = true;
            }
        }

        $this->options[$name] = $value;
    }

    /**
     * Adds an argument value.
     *
     * @throws InvalidArgumentException When argument given doesn't exist
     */
    private function addArgument(string|int $name, mixed $value): void
    {
        if (!$this->definition->hasArgument($name)) {
            throw new InvalidArgumentException(\sprintf('The "%s" argument does not exist.', $name));
        }

        $this->arguments[$name] = $value;
    }
}
