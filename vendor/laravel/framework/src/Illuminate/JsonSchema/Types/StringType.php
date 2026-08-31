<?php

namespace Illuminate\JsonSchema\Types;

class StringType extends Type
{
    /**
     * The minimum length (inclusive).
     */
    protected ?int $minLength = null;

    /**
     * The maximum length (inclusive).
     */
    protected ?int $maxLength = null;

    /**
     * A regular expression the value must match.
     */
    protected ?string $pattern = null;

    /**
     * The format of the string.
     */
    protected ?string $format = null;

    /**
     * Set the minimum length (inclusive).
     */
    public function min(int $value): static
    {
        $this->minLength = $value;

        return $this;
    }

    /**
     * Set the maximum length (inclusive).
     */
    public function max(int $value): static
    {
        $this->maxLength = $value;

        return $this;
    }

    /**
     * Set the pattern the value must satisfy.
     */
    public function pattern(string $value): static
    {
        $this->pattern = $value;

        return $this;
    }

    /**
     * Set the format of the string.
     *
     * {@link https://json-schema.org/understanding-json-schema/reference/type#built-in-formats}
     */
    public function format(string $value): static
    {
        $this->format = $value;

        return $this;
    }

    /**
     * Set the type's default value.
     */
    public function default(string $value): static
    {
        $this->default = $value;

        return $this;
    }
}
