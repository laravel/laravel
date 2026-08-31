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
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * Input is the base class for all concrete Input classes.
 *
 * Three concrete classes are provided by default:
 *
 *  * `ArgvInput`: The input comes from the CLI arguments (argv)
 *  * `StringInput`: The input is provided as a string
 *  * `ArrayInput`: The input is provided as an array
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Input implements InputInterface, StreamableInputInterface
{
    protected InputDefinition $definition;
    /** @var resource */
    protected $stream;
    protected array $options = [];
    protected array $arguments = [];
    protected bool $interactive = true;

    public function __construct(?InputDefinition $definition = null)
    {
        if (null === $definition) {
            $this->definition = new InputDefinition();
        } else {
            $this->bind($definition);
            $this->validate();
        }
    }

    public function bind(InputDefinition $definition): void
    {
        $this->arguments = [];
        $this->options = [];
        $this->definition = $definition;

        $this->parse();
    }

    /**
     * Processes command line arguments.
     */
    abstract protected function parse(): void;

    public function validate(): void
    {
        $definition = $this->definition;
        $givenArguments = $this->arguments;

        $missingArguments = array_filter(array_keys($definition->getArguments()), static fn ($argument) => !\array_key_exists($argument, $givenArguments) && $definition->getArgument($argument)->isRequired());

        if (\count($missingArguments) > 0) {
            throw new RuntimeException(\sprintf('Not enough arguments (missing: "%s").', implode(', ', $missingArguments)));
        }
    }

    public function isInteractive(): bool
    {
        return $this->interactive;
    }

    public function setInteractive(bool $interactive): void
    {
        $this->interactive = $interactive;
    }

    public function getArguments(): array
    {
        return array_merge($this->definition->getArgumentDefaults(), $this->arguments);
    }

    public function getArgument(string $name): mixed
    {
        if (!$this->definition->hasArgument($name)) {
            throw new InvalidArgumentException(\sprintf('The "%s" argument does not exist.', $name));
        }

        return $this->arguments[$name] ?? $this->definition->getArgument($name)->getDefault();
    }

    public function setArgument(string $name, mixed $value): void
    {
        if (!$this->definition->hasArgument($name)) {
            throw new InvalidArgumentException(\sprintf('The "%s" argument does not exist.', $name));
        }

        $this->arguments[$name] = $value;
    }

    public function hasArgument(string $name): bool
    {
        return $this->definition->hasArgument($name);
    }

    public function getOptions(): array
    {
        return array_merge($this->definition->getOptionDefaults(), $this->options);
    }

    public function getOption(string $name): mixed
    {
        if ($this->definition->hasNegation($name)) {
            if (null === $value = $this->getOption($this->definition->negationToName($name))) {
                return $value;
            }

            return !$value;
        }

        if (!$this->definition->hasOption($name)) {
            throw new InvalidArgumentException(\sprintf('The "%s" option does not exist.', $name));
        }

        return \array_key_exists($name, $this->options) ? $this->options[$name] : $this->definition->getOption($name)->getDefault();
    }

    public function setOption(string $name, mixed $value): void
    {
        if ($this->definition->hasNegation($name)) {
            $this->options[$this->definition->negationToName($name)] = !$value;

            return;
        } elseif (!$this->definition->hasOption($name)) {
            throw new InvalidArgumentException(\sprintf('The "%s" option does not exist.', $name));
        }

        $this->options[$name] = $value;
    }

    public function hasOption(string $name): bool
    {
        return $this->definition->hasOption($name) || $this->definition->hasNegation($name);
    }

    /**
     * Escapes a token through escapeshellarg if it contains unsafe chars.
     */
    public function escapeToken(string $token): string
    {
        return preg_match('{^[\w-]+$}', $token) ? $token : escapeshellarg($token);
    }

    /**
     * @param resource $stream
     */
    public function setStream($stream): void
    {
        $this->stream = $stream;
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }
}
