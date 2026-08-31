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
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\String\UnicodeString;

#[\Attribute(\Attribute::TARGET_PARAMETER | \Attribute::TARGET_PROPERTY)]
class Option
{
    private const ALLOWED_TYPES = ['string', 'bool', 'int', 'float', 'array'];
    private const ALLOWED_UNION_TYPES = ['bool|string', 'bool|int', 'bool|float'];

    private string|bool|int|float|array|null $default = null;
    private array|\Closure $suggestedValues;
    private ?int $mode = null;
    /**
     * @var string|class-string<\BackedEnum>
     */
    private string $typeName = '';
    private bool $allowNull = false;
    private string $memberName = '';
    private string $sourceName = '';

    /**
     * Represents a console command --option definition.
     *
     * If unset, the `name` value will be inferred from the parameter definition.
     *
     * @param array|string|null                                                          $shortcut        The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
     * @param array<string|Suggestion>|callable(CompletionInput):list<string|Suggestion> $suggestedValues The values used for input completion
     */
    public function __construct(
        public string $description = '',
        public string $name = '',
        public array|string|null $shortcut = null,
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

        $self->memberName = $reflection->getMemberName();
        $self->sourceName = $reflection->getSourceName();

        $name = $reflection->getName();
        $type = $reflection->getType();

        if (!$reflection->hasDefaultValue()) {
            throw new LogicException(\sprintf('The option %s "$%s" of "%s" must declare a default value.', $self->memberName, $name, $self->sourceName));
        }

        if (!$self->name) {
            $self->name = (new UnicodeString($name))->kebab();
        }

        $self->default = $reflection->getDefaultValue();
        $self->allowNull = $reflection->isNullable();

        if ($type instanceof \ReflectionUnionType) {
            return $self->handleUnion($type);
        }

        if (!$type instanceof \ReflectionNamedType) {
            throw new LogicException(\sprintf('The %s "$%s" of "%s" must have a named type. Untyped or Intersection types are not supported for command options.', $self->memberName, $name, $self->sourceName));
        }

        $self->typeName = $type->getName();
        $isBackedEnum = is_subclass_of($self->typeName, \BackedEnum::class);

        if (!\in_array($self->typeName, self::ALLOWED_TYPES, true) && !$isBackedEnum) {
            throw new LogicException(\sprintf('The type "%s" on %s "$%s" of "%s" is not supported as a command option. Only "%s" types and BackedEnum are allowed.', $self->typeName, $self->memberName, $name, $self->sourceName, implode('", "', self::ALLOWED_TYPES)));
        }

        if ('bool' === $self->typeName && $self->allowNull && \in_array($self->default, [true, false], true)) {
            throw new LogicException(\sprintf('The option %s "$%s" of "%s" must not be nullable when it has a default boolean value.', $self->memberName, $name, $self->sourceName));
        }

        if ($self->allowNull && null !== $self->default) {
            throw new LogicException(\sprintf('The option %s "$%s" of "%s" must either be not-nullable or have a default of null.', $self->memberName, $name, $self->sourceName));
        }

        if ('bool' === $self->typeName) {
            $self->mode = InputOption::VALUE_NONE;
            if (false !== $self->default) {
                $self->mode |= InputOption::VALUE_NEGATABLE;
            }
        } elseif ('array' === $self->typeName) {
            $self->mode = InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY;
        } else {
            $self->mode = InputOption::VALUE_REQUIRED;
        }

        if (\is_array($self->suggestedValues) && !\is_callable($self->suggestedValues) && 2 === \count($self->suggestedValues) && ($instance = $reflection->getSourceThis()) && $instance::class === $self->suggestedValues[0] && \is_callable([$instance, $self->suggestedValues[1]])) {
            $self->suggestedValues = [$instance, $self->suggestedValues[1]];
        }

        if ($isBackedEnum && !$self->suggestedValues) {
            $self->suggestedValues = array_column($self->typeName::cases(), 'value');
        }

        return $self;
    }

    /**
     * @internal
     */
    public function toInputOption(): InputOption
    {
        $default = InputOption::VALUE_NONE === (InputOption::VALUE_NONE & $this->mode) ? null : $this->default;
        $suggestedValues = \is_callable($this->suggestedValues) ? ($this->suggestedValues)(...) : $this->suggestedValues;

        return new InputOption($this->name, $this->shortcut, $this->mode, $this->description, $default, $suggestedValues);
    }

    /**
     * @internal
     */
    public function resolveValue(InputInterface $input): mixed
    {
        $value = $input->getOption($this->name);

        if (null === $value && \in_array($this->typeName, self::ALLOWED_UNION_TYPES, true)) {
            return true;
        }

        if (is_subclass_of($this->typeName, \BackedEnum::class) && (\is_string($value) || \is_int($value))) {
            return $this->typeName::tryFrom($value) ?? throw InvalidOptionException::fromEnumValue($this->name, $value, $this->suggestedValues);
        }

        if ('array' === $this->typeName && $this->allowNull && [] === $value) {
            return null;
        }

        if ('bool' !== $this->typeName) {
            return $value;
        }

        if ($this->allowNull && null === $value) {
            return null;
        }

        return $value ?? $this->default;
    }

    private function handleUnion(\ReflectionUnionType $type): self
    {
        $types = array_map(
            static fn (\ReflectionType $t) => $t instanceof \ReflectionNamedType ? $t->getName() : null,
            $type->getTypes(),
        );

        sort($types);

        $this->typeName = implode('|', array_filter($types));

        if (!\in_array($this->typeName, self::ALLOWED_UNION_TYPES, true)) {
            throw new LogicException(\sprintf('The union type for %s "$%s" of "%s" is not supported as a command option. Only "%s" types are allowed.', $this->memberName, $this->name, $this->sourceName, implode('", "', self::ALLOWED_UNION_TYPES)));
        }

        if (false !== $this->default) {
            throw new LogicException(\sprintf('The option %s "$%s" of "%s" must have a default value of false.', $this->memberName, $this->name, $this->sourceName));
        }

        $this->mode = InputOption::VALUE_OPTIONAL;

        return $this;
    }
}
