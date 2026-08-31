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
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\String\UnicodeString;

#[\Attribute(\Attribute::TARGET_PARAMETER | \Attribute::TARGET_PROPERTY)]
class Argument
{
    private const ALLOWED_TYPES = ['string', 'bool', 'int', 'float', 'array'];

    private string|bool|int|float|array|null $default = null;
    private array|\Closure $suggestedValues;
    private ?int $mode = null;
    /**
     * @var string|class-string<\BackedEnum>
     */
    private string $typeName = '';
    private ?InteractiveAttributeInterface $interactiveAttribute = null;

    /**
     * Represents a console command <argument> definition.
     *
     * If unset, the `name` value will be inferred from the parameter definition.
     *
     * @param array<string|Suggestion>|callable(CompletionInput):list<string|Suggestion> $suggestedValues The values used for input completion
     */
    public function __construct(
        public string $description = '',
        public string $name = '',
        array|callable $suggestedValues = [],
    ) {
        $this->suggestedValues = \is_callable($suggestedValues) ? $suggestedValues(...) : $suggestedValues;
    }

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
        $name = $reflection->getName();

        if (!$type instanceof \ReflectionNamedType) {
            throw new LogicException(\sprintf('The %s "$%s" of "%s" must have a named type. Untyped, Union or Intersection types are not supported for command arguments.', $reflection->getMemberName(), $name, $reflection->getSourceName()));
        }

        $self->typeName = $type->getName();
        $isBackedEnum = is_subclass_of($self->typeName, \BackedEnum::class);

        if (!\in_array($self->typeName, self::ALLOWED_TYPES, true) && !$isBackedEnum) {
            throw new LogicException(\sprintf('The type "%s" on %s "$%s" of "%s" is not supported as a command argument. Only "%s" types and backed enums are allowed.', $self->typeName, $reflection->getMemberName(), $name, $reflection->getSourceName(), implode('", "', self::ALLOWED_TYPES)));
        }

        if (!$self->name) {
            $self->name = (new UnicodeString($name))->kebab();
        }

        $self->default = $reflection->hasDefaultValue() ? $reflection->getDefaultValue() : null;

        $isOptional = $reflection->hasDefaultValue() || $reflection->isNullable();
        $self->mode = $isOptional ? InputArgument::OPTIONAL : InputArgument::REQUIRED;
        if ('array' === $self->typeName) {
            $self->mode |= InputArgument::IS_ARRAY;
        }

        if (\is_array($self->suggestedValues) && !\is_callable($self->suggestedValues) && 2 === \count($self->suggestedValues) && ($instance = $reflection->getSourceThis()) && $instance::class === $self->suggestedValues[0] && \is_callable([$instance, $self->suggestedValues[1]])) {
            // In case that the callback is declared as a static method `[Foo::class, 'methodName']` - yet it is not callable,
            // while non-static method `[Foo $instance, 'methodName']` would be callable, we transform the callback on the fly into a non-static version.
            $self->suggestedValues = [$instance, $self->suggestedValues[1]];
        }

        if ($isBackedEnum && !$self->suggestedValues) {
            $self->suggestedValues = array_column($self->typeName::cases(), 'value');
        }

        $self->interactiveAttribute = Ask::tryFrom($member, $self->name);

        if ($self->interactiveAttribute && $isOptional) {
            throw new LogicException(\sprintf('The %s "$%s" argument of "%s" cannot be both interactive and optional.', $reflection->getMemberName(), $self->name, $reflection->getSourceName()));
        }

        return $self;
    }

    /**
     * @internal
     */
    public function toInputArgument(): InputArgument
    {
        $suggestedValues = \is_callable($this->suggestedValues) ? ($this->suggestedValues)(...) : $this->suggestedValues;

        return new InputArgument($this->name, $this->mode, $this->description, $this->default, $suggestedValues);
    }

    /**
     * @internal
     */
    public function resolveValue(InputInterface $input): mixed
    {
        $value = $input->getArgument($this->name);

        if (is_subclass_of($this->typeName, \BackedEnum::class) && (\is_string($value) || \is_int($value))) {
            return $this->typeName::tryFrom($value) ?? throw InvalidArgumentException::fromEnumValue($this->name, $value, $this->suggestedValues);
        }

        return $value;
    }

    /**
     * @internal
     */
    public function getInteractiveAttribute(): ?InteractiveAttributeInterface
    {
        return $this->interactiveAttribute;
    }

    /**
     * @internal
     */
    public function isRequired(): bool
    {
        return InputArgument::REQUIRED === (InputArgument::REQUIRED & $this->mode);
    }
}
