<?php

namespace Illuminate\JsonSchema\Types;

class ObjectType extends Type
{
    /**
     * Whether additional properties are allowed.
     */
    protected ?bool $additionalProperties = null;

    /**
     * Create a new object type instance.
     *
     * @param  array<string, Type>  $properties
     */
    public function __construct(protected array $properties = [])
    {
        //
    }

    /**
     * Disallow additional properties.
     */
    public function withoutAdditionalProperties(): static
    {
        $this->additionalProperties = false;

        return $this;
    }

    /**
     * Set the type's default value.
     *
     * @param  array<string, mixed>  $value
     */
    public function default(array $value): static
    {
        $this->default = $value;

        return $this;
    }
}
