<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Attribute;

use Symfony\Component\Console\Attribute\Reflection\ReflectionMember;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Interaction\Interaction;

/**
 * Maps a command input into an object (DTO).
 */
#[\Attribute(\Attribute::TARGET_PARAMETER | \Attribute::TARGET_PROPERTY)]
final class MapInput
{
    /**
     * @var array<string, Argument|Option|self>
     */
    private array $definition = [];

    private \ReflectionClass $class;

    /**
     * @var list<Interact>
     */
    private array $interactiveAttributes = [];

    /**
     * @internal
     */
    public static function tryFrom(\ReflectionParameter|\ReflectionProperty $member): ?self
    {
        $reflection = new ReflectionMember($member);

        if (!$self = $reflection->getAttribute(self::class)) {
            return null;
        }

        $type = $reflection->getType();

        if (!$type instanceof \ReflectionNamedType) {
            throw new LogicException(\sprintf('The input %s "%s" must have a named type.', $reflection->getMemberName(), $member->name));
        }

        if (!class_exists($class = $type->getName())) {
            throw new LogicException(\sprintf('The input class "%s" does not exist.', $type->getName()));
        }

        $self->class = new \ReflectionClass($class);

        foreach ($self->class->getProperties() as $property) {
            if ($argument = Argument::tryFrom($property)) {
                $self->definition[$property->name] = $argument;
            } elseif ($option = Option::tryFrom($property)) {
                $self->definition[$property->name] = $option;
            } elseif ($input = self::tryFrom($property)) {
                $self->definition[$property->name] = $input;
            }

            if (isset($self->definition[$property->name]) && (!$property->isPublic() || $property->isStatic())) {
                throw new LogicException(\sprintf('The input property "%s::$%s" must be public and non-static.', $self->class->name, $property->name));
            }
        }

        if (!$self->definition) {
            throw new LogicException(\sprintf('The input class "%s" must have at least one argument or option.', $self->class->name));
        }

        foreach ($self->class->getMethods() as $method) {
            if ($attribute = Interact::tryFrom($method)) {
                $self->interactiveAttributes[] = $attribute;
            }
        }

        return $self;
    }

    /**
     * @internal
     */
    public function resolveValue(InputInterface $input): object
    {
        $instance = $this->class->newInstanceWithoutConstructor();

        foreach ($this->definition as $name => $spec) {
            // ignore required arguments that are not set yet (may happen in interactive mode)
            if ($spec instanceof Argument && $spec->isRequired() && \in_array($input->getArgument($spec->name), [null, []], true)) {
                continue;
            }

            $instance->$name = $spec->resolveValue($input);
        }

        return $instance;
    }

    /**
     * @internal
     */
    public function setValue(InputInterface $input, object $object): void
    {
        foreach ($this->definition as $name => $spec) {
            $property = $this->class->getProperty($name);

            if (!$property->isInitialized($object) || \in_array($value = $property->getValue($object), [null, []], true)) {
                continue;
            }

            match (true) {
                $spec instanceof Argument => $input->setArgument($spec->name, $value),
                $spec instanceof Option => $input->setOption($spec->name, $value),
                $spec instanceof self => $spec->setValue($input, $value),
                default => throw new LogicException('Unexpected specification type.'),
            };
        }
    }

    /**
     * @return iterable<Argument>
     */
    public function getArguments(): iterable
    {
        foreach ($this->definition as $spec) {
            if ($spec instanceof Argument) {
                yield $spec;
            } elseif ($spec instanceof self) {
                yield from $spec->getArguments();
            }
        }
    }

    /**
     * @return iterable<Option>
     */
    public function getOptions(): iterable
    {
        foreach ($this->definition as $spec) {
            if ($spec instanceof Option) {
                yield $spec;
            } elseif ($spec instanceof self) {
                yield from $spec->getOptions();
            }
        }
    }

    /**
     * @internal
     *
     * @return iterable<Interaction>
     */
    public function getPropertyInteractions(): iterable
    {
        foreach ($this->definition as $spec) {
            if ($spec instanceof self) {
                yield from $spec->getPropertyInteractions();
            } elseif ($spec instanceof Argument && $attribute = $spec->getInteractiveAttribute()) {
                yield new Interaction($this, $attribute);
            }
        }
    }

    /**
     * @internal
     *
     * @return iterable<Interaction>
     */
    public function getMethodInteractions(): iterable
    {
        foreach ($this->definition as $spec) {
            if ($spec instanceof self) {
                yield from $spec->getMethodInteractions();
            }
        }

        foreach ($this->interactiveAttributes as $attribute) {
            yield new Interaction($this, $attribute);
        }
    }
}
