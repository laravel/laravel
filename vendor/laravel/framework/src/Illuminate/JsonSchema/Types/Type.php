<?php

namespace Illuminate\JsonSchema\Types;

use BackedEnum;
use Illuminate\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Serializer;
use InvalidArgumentException;

abstract class Type extends JsonSchema
{
    /**
     * Whether the type is required.
     */
    protected ?bool $required = null;

    /**
     * The type's title.
     */
    protected ?string $title = null;

    /**
     * The type's description.
     */
    protected ?string $description = null;

    /**
     * The default value for the type.
     */
    protected mixed $default = null;

    /**
     * The set of allowed values for the type.
     *
     * @var array<int, mixed>|null
     */
    protected ?array $enum = null;

    /**
     * Indicates if the type is nullable.
     */
    protected ?bool $nullable = null;

    /**
     * Indicate that the type is required.
     */
    public function required(bool $required = true): static
    {
        if ($required) {
            $this->required = true;
        }

        return $this;
    }

    /**
     * Indicate that the type is optional.
     */
    public function nullable(bool $nullable = true): static
    {
        if ($nullable) {
            $this->nullable = true;
        }

        return $this;
    }

    /**
     * Set the type's title.
     */
    public function title(string $value): static
    {
        $this->title = $value;

        return $this;
    }

    /**
     * Set the type's description.
     */
    public function description(string $value): static
    {
        $this->description = $value;

        return $this;
    }

    /**
     * Restrict the value to one of the provided enumerated values.
     *
     * @param  class-string<\BackedEnum>|array<int, mixed>  $values
     *
     * @throws \InvalidArgumentException
     */
    public function enum(array|string $values): static
    {
        if (is_string($values)) {
            if (! is_subclass_of($values, BackedEnum::class)) {
                throw new InvalidArgumentException('The provided class must be a BackedEnum.');
            }

            $values = array_column($values::cases(), 'value');
        }

        // Keep order and allow complex values (arrays / objects) without forcing uniqueness...
        $this->enum = array_values($values);

        return $this;
    }

    /**
     * Convert the type to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return Serializer::serialize($this);
    }

    /**
     * Convert the type to its string representation.
     */
    public function toString(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT) ?: '';
    }

    /**
     * Convert the type to its string representation.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
